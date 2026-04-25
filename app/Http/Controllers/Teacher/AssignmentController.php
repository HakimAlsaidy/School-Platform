<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Classroom;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user()->teacher;

        $query = Assignment::with(['classroom.grade', 'subject', 'submissions'])
            ->where('teacher_id', $teacher->id);

        if ($request->filled('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $assignments = $query->latest()->paginate(15);
        $classrooms = $teacher->classrooms()->with('grade')->get();
        $subjects = $teacher->subjects;

        return view('teacher.assignments.index', compact('assignments', 'classrooms', 'subjects'));
    }

    public function create()
    {
        $teacher = Auth::user()->teacher;
        $classrooms = $teacher->classrooms()->with('grade')->get();
        $subjects = $teacher->subjects;

        return view('teacher.assignments.create', compact('classrooms', 'subjects'));
    }

    public function store(Request $request)
    {
        $teacher = Auth::user()->teacher;

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'due_date' => ['required', 'date', 'after:now'],
            'max_score' => ['required', 'numeric', 'min:1', 'max:100'],
        ]);

        $validated['teacher_id'] = $teacher->id;

        $assignment = Assignment::create($validated);

        ActivityLog::log('create_assignment', "إنشاء واجب: {$assignment->title}", $assignment);

        return redirect()->route('teacher.assignments.index')
            ->with('success', 'تم إنشاء الواجب بنجاح.');
    }

    public function show(Assignment $assignment)
    {
        $this->authorize('view', $assignment);

        $assignment->load([
            'classroom.grade',
            'subject',
            'submissions.student',
        ]);

        $students = $assignment->classroom->students;
        $submittedIds = $assignment->submissions->pluck('student_id')->toArray();

        return view('teacher.assignments.show', compact('assignment', 'students', 'submittedIds'));
    }

    public function edit(Assignment $assignment)
    {
        $this->authorize('update', $assignment);

        $teacher = Auth::user()->teacher;
        $classrooms = $teacher->classrooms()->with('grade')->get();
        $subjects = $teacher->subjects;

        return view('teacher.assignments.edit', compact('assignment', 'classrooms', 'subjects'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $this->authorize('update', $assignment);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'due_date' => ['required', 'date'],
            'max_score' => ['required', 'numeric', 'min:1', 'max:100'],
        ]);

        $assignment->update($validated);

        ActivityLog::log('update_assignment', "تحديث الواجب: {$assignment->title}", $assignment);

        return redirect()->route('teacher.assignments.index')
            ->with('success', 'تم تحديث الواجب بنجاح.');
    }

    public function destroy(Assignment $assignment)
    {
        $this->authorize('delete', $assignment);

        $title = $assignment->title;
        $assignment->delete();

        ActivityLog::log('delete_assignment', "حذف الواجب: {$title}");

        return redirect()->route('teacher.assignments.index')
            ->with('success', 'تم حذف الواجب بنجاح.');
    }

    public function gradeSubmission(Request $request, AssignmentSubmission $submission)
    {
        $validated = $request->validate([
            'score' => ['required', 'numeric', 'min:0', 'max:' . $submission->assignment->max_score],
            'feedback' => ['nullable', 'string', 'max:1000'],
        ]);

        $submission->update($validated);

        ActivityLog::log('grade_submission', "تقييم تسليم الواجب", $submission);

        return redirect()->back()->with('success', 'تم تقييم الواجب بنجاح.');
    }
}
