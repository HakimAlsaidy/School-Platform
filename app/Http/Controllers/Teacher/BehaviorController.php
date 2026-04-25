<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Behavior;
use App\Models\Classroom;
use App\Models\Notification;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BehaviorController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $classrooms = $teacher->classrooms()->with('grade')->get();

        $query = Behavior::with(['student.classroom.grade'])
            ->where('teacher_id', $teacher->id);

        if ($request->filled('classroom_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('classroom_id', $request->classroom_id);
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $behaviors = $query->latest()->paginate(15);

        return view('teacher.behaviors.index', compact('behaviors', 'classrooms'));
    }

    public function create()
    {
        $teacher = Auth::user()->teacher;
        $classrooms = $teacher->classrooms()->with(['grade', 'students'])->get();
        $subjects = $teacher->subjects;

        return view('teacher.behaviors.create', compact('classrooms', 'subjects'));
    }

    public function store(Request $request)
    {
        $teacher = Auth::user()->teacher;

        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'type' => ['required', 'in:positive,negative'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'points' => ['required', 'integer', 'min:-100', 'max:100'],
            'date' => ['required', 'date', 'before_or_equal:today'],
        ]);

        $validated['teacher_id'] = $teacher->id;

        $behavior = Behavior::create($validated);

        // إرسال إشعار لولي الأمر
        $student = Student::with('guardian.user')->find($validated['student_id']);
        if ($student && $student->guardian && $student->guardian->user) {
            $typeText = $validated['type'] === 'positive' ? 'إيجابي' : 'سلبي';
            
            Notification::send(
                $student->guardian->user->id,
                "سلوك {$typeText}",
                "{$student->name}: {$validated['title']}",
                'behavior',
                route('parent.students.behaviors', $student),
                'عرض السلوك'
            );
        }

        ActivityLog::log('create_behavior', "تسجيل سلوك للطالب", $behavior);

        return redirect()->route('teacher.behaviors.index')
            ->with('success', 'تم تسجيل السلوك بنجاح.');
    }

    public function destroy(Behavior $behavior)
    {
        $this->authorize('delete', $behavior);

        $behavior->delete();

        ActivityLog::log('delete_behavior', "حذف تسجيل سلوك");

        return redirect()->back()->with('success', 'تم حذف السلوك بنجاح.');
    }
}
