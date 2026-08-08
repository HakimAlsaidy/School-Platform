<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\OnlineQuiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function index($studentId)
    {
        $guardian = Auth::user()->guardian;
        $student = $guardian->students()->findOrFail($studentId);

        $quizzes = OnlineQuiz::with(['subject', 'questions'])
            ->where('classroom_id', $student->classroom_id)
            ->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('start_at')->orWhere('start_at', '<=', now());
            })
            ->latest()
            ->get();

        // جلب محاولات الطالب
        $attempts = QuizAttempt::where('student_id', $student->id)
            ->get()
            ->keyBy('online_quiz_id');

        return view('guardian.quizzes.index', compact('student', 'quizzes', 'attempts'));
    }

    public function show($studentId, OnlineQuiz $quiz)
    {
        $guardian = Auth::user()->guardian;
        $student = $guardian->students()->findOrFail($studentId);

        // التحقق من أن الطالب في نفس فصل الاختبار
        abort_unless($student->classroom_id === $quiz->classroom_id, 403);
        abort_unless($quiz->is_published, 403);

        // التحقق من عدم وجود إعادة
        $existingAttempt = QuizAttempt::where('student_id', $student->id)
            ->where('online_quiz_id', $quiz->id)
            ->where('status', 'submitted')
            ->first();

        if ($existingAttempt && !$quiz->allow_retake) {
            return redirect()->route('parent.quizzes.results', [$student->id, $existingAttempt->id])
                ->with('info', 'لقد أكملت هذا الاختبار مسبقاً');
        }

        $quiz->load('questions');

        return view('guardian.quizzes.show', compact('student', 'quiz'));
    }

    public function submit($studentId, OnlineQuiz $quiz, Request $request)
    {
        $guardian = Auth::user()->guardian;
        $student = $guardian->students()->findOrFail($studentId);
        abort_unless($student->classroom_id === $quiz->classroom_id, 403);

        $quiz->load('questions');

        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['nullable', 'string'],
        ]);

        // حساب النتيجة
        $score = 0;
        $maxScore = 0;
        $answers = [];

        foreach ($quiz->questions as $question) {
            $maxScore += $question->points;
            $userAnswer = $validated['answers'][$question->id] ?? '';
            $isCorrect = $this->checkAnswer($question->type, $userAnswer, $question->correct_answer);

            if ($isCorrect) {
                $score += $question->points;
            }

            $answers[$question->id] = [
                'answer' => $userAnswer,
                'correct' => $isCorrect,
                'correct_answer' => $question->correct_answer,
            ];
        }

        $attempt = QuizAttempt::create([
            'online_quiz_id' => $quiz->id,
            'student_id' => $student->id,
            'score' => $score,
            'max_score' => $maxScore,
            'answers' => $answers,
            'started_at' => now()->subMinutes($quiz->duration_minutes),
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        return redirect()->route('parent.quizzes.results', [$student->id, $attempt->id])
            ->with('success', 'تم إرسال الاختبار بنجاح.');
    }

    public function results($studentId, QuizAttempt $attempt)
    {
        $guardian = Auth::user()->guardian;
        $student = $guardian->students()->findOrFail($studentId);
        abort_unless($attempt->student_id === $student->id, 403);

        $attempt->load(['quiz.subject', 'quiz.questions']);

        return view('guardian.quizzes.results', compact('student', 'attempt'));
    }

    private function checkAnswer(string $type, string $userAnswer, string $correctAnswer): bool
    {
        if ($type === 'essay') {
            return false; // المقالي يُصحح يدوياً
        }

        $userAnswer = trim(mb_strtolower($userAnswer));
        $correctAnswer = trim(mb_strtolower($correctAnswer));

        // مقارنة مرنة لاختيار من متعدد
        if ($type === 'multiple_choice') {
            return $userAnswer === $correctAnswer;
        }

        return $userAnswer === $correctAnswer;
    }
}
