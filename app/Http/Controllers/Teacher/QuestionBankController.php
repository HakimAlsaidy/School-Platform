<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Grade;
use App\Models\QuestionBank;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionBankController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $subjects = $teacher->subjects;
        $grades = Grade::all();

        $query = QuestionBank::with(['subject', 'grade'])
            ->where('teacher_id', $teacher->id);

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('grade_id')) {
            $query->where('grade_id', $request->grade_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $questions = $query->latest()->paginate(15);

        return view('teacher.question-bank.index', compact('questions', 'subjects', 'grades'));
    }

    public function store(Request $request)
    {
        $teacher = Auth::user()->teacher;

        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'grade_id' => ['nullable', 'exists:grades,id'],
            'type' => ['required', 'in:multiple_choice,true_false,short_answer,essay'],
            'question' => ['required', 'string'],
            'options' => ['nullable', 'array'],
            'options.*' => ['nullable', 'string'],
            'correct_answer' => ['required', 'string'],
            'points' => ['required', 'integer', 'min:1', 'max:100'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
        ]);

        // تنظيف الخيارات
        $options = array_values(array_filter($validated['options'] ?? []));

        QuestionBank::create([
            'school_id' => auth()->user()->school_id,
            'subject_id' => $validated['subject_id'],
            'grade_id' => $validated['grade_id'] ?? null,
            'teacher_id' => $teacher->id,
            'type' => $validated['type'],
            'question' => $validated['question'],
            'options' => $options ?: null,
            'correct_answer' => $validated['correct_answer'],
            'points' => $validated['points'],
            'difficulty' => $validated['difficulty'],
        ]);

        ActivityLog::log('add_question', 'إضافة سؤال إلى بنك الأسئلة');
        return back()->with('success', 'تمت إضافة السؤال إلى بنك الأسئلة بنجاح.');
    }

    public function destroy(QuestionBank $question)
    {
        $question->delete();
        return back()->with('success', 'تم حذف السؤال.');
    }
}
