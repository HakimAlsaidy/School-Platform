<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Classroom;
use App\Models\Notification;
use App\Models\OnlineQuiz;
use App\Models\QuestionBank;
use App\Models\QuizQuestion;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        $quizzes = OnlineQuiz::with(['subject', 'classroom.grade', 'questions'])
            ->where('teacher_id', $teacher->id)
            ->latest()
            ->paginate(15);

        return view('teacher.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        $teacher = Auth::user()->teacher;
        $classrooms = $teacher->classrooms()->with('grade')->get();
        $subjects = $teacher->subjects;

        return view('teacher.quizzes.create', compact('classrooms', 'subjects'));
    }

    public function store(Request $request)
    {
        $teacher = Auth::user()->teacher;

        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:300'],
            'total_points' => ['required', 'integer', 'min:1'],
            'is_published' => ['nullable'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after:start_at'],
            'allow_retake' => ['nullable'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.question' => ['required', 'string'],
            'questions.*.type' => ['required', 'in:multiple_choice,true_false,short_answer,essay'],
            'questions.*.options' => ['nullable', 'array'],
            'questions.*.options.*' => ['nullable', 'string'],
            'questions.*.correct_answer' => ['required', 'string'],
            'questions.*.points' => ['required', 'integer', 'min:1'],
        ]);

        $quiz = OnlineQuiz::create([
            'school_id' => auth()->user()->school_id,
            'subject_id' => $validated['subject_id'],
            'teacher_id' => $teacher->id,
            'classroom_id' => $validated['classroom_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'duration_minutes' => $validated['duration_minutes'],
            'total_points' => $validated['total_points'],
            'is_published' => $request->has('is_published'),
            'start_at' => $validated['start_at'] ?? null,
            'end_at' => $validated['end_at'] ?? null,
            'allow_retake' => $request->has('allow_retake'),
        ]);

        // إضافة الأسئلة
        foreach ($validated['questions'] as $order => $q) {
            $options = array_values(array_filter($q['options'] ?? []));
            QuizQuestion::create([
                'online_quiz_id' => $quiz->id,
                'question' => $q['question'],
                'type' => $q['type'],
                'options' => $options ?: null,
                'correct_answer' => $q['correct_answer'],
                'points' => $q['points'],
                'order' => $order,
            ]);
        }

        // إشعار للطلاب
        $students = Student::where('classroom_id', $validated['classroom_id'])
            ->where('is_active', true)
            ->get();
        foreach ($students as $student) {
            if ($student->guardian && $student->guardian->user) {
                Notification::send(
                    $student->guardian->user->id,
                    'اختبار إلكتروني جديد',
                    "تم نشر اختبار: {$quiz->title}",
                    'schedule',
                    route('parent.students.quizzes'),
                    'بدء الاختبار'
                );
            }
        }

        ActivityLog::log('create_quiz', "إنشاء اختبار إلكتروني: {$quiz->title}", $quiz);
        return redirect()->route('teacher.quizzes.index')
            ->with('success', 'تم إنشاء الاختبار بنجاح.');
    }

    public function show(OnlineQuiz $quiz)
    {
        $quiz->load(['questions', 'subject', 'classroom.grade', 'attempts.student']);
        $teacher = Auth::user()->teacher;
        $bankQuestions = QuestionBank::with(['subject', 'grade'])
            ->where('teacher_id', $teacher->id)
            ->latest()
            ->take(50)
            ->get();
        return view('teacher.quizzes.show', compact('quiz', 'bankQuestions'));
    }

    public function togglePublish(OnlineQuiz $quiz)
    {
        $quiz->update(['is_published' => !$quiz->is_published]);
        $status = $quiz->is_published ? 'نُشر' : 'أُوقف';
        return back()->with('success', "تم {$status} الاختبار بنجاح.");
    }

    public function destroy(OnlineQuiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('teacher.quizzes.index')
            ->with('success', 'تم حذف الاختبار.');
    }

    // جلب أسئلة من بنك الأسئلة
    public function importFromBank(OnlineQuiz $quiz, Request $request)
    {
        $request->validate([
            'question_ids' => ['required', 'array'],
            'question_ids.*' => ['exists:question_banks,id'],
        ]);

        $maxOrder = $quiz->questions()->max('order') ?? 0;
        $order = $maxOrder + 1;

        foreach ($request->question_ids as $id) {
            $q = QuestionBank::find($id);
            if (!$q) continue;

            QuizQuestion::create([
                'online_quiz_id' => $quiz->id,
                'question_bank_id' => $q->id,
                'question' => $q->question,
                'type' => $q->type,
                'options' => $q->options,
                'correct_answer' => $q->correct_answer,
                'points' => $q->points,
                'order' => $order++,
            ]);
        }

        return back()->with('success', 'تم استيراد الأسئلة بنجاح.');
    }
}
