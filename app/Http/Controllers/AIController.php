<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Student;
use App\Services\AIService;
use Illuminate\Http\Request;

class AIController extends Controller
{
    protected AIService $ai;

    public function __construct(AIService $ai)
    {
        $this->ai = $ai;
    }

    /**
     * عرض لوحة التحليلات الذكية
     */
    public function analytics(Request $request)
    {
        $user = auth()->user();

        // للطلاب تحت مسؤولية ولي الأمر
        $children = collect();
        if ($user->isParent()) {
            $children = $user->guardian?->students()->get() ?? collect();
        }

        $selectedStudentId = $request->get('student_id');
        $studentInsights = null;

        if ($selectedStudentId) {
            $student = Student::find($selectedStudentId);

            // التحقق من صلاحية الوصول
            if ($student && $this->canAccessStudent($student)) {
                $studentInsights = $this->ai->studentInsights($student);
            }
        }

        $schoolAnalytics = $this->ai->schoolAnalytics();
        $grades = Grade::withCount('students')->get();

        // للتحقق من سياق الطالب في المساعد
        $contextStudent = null;
        if ($selectedStudentId) {
            $contextStudent = Student::find($selectedStudentId);
            if ($contextStudent && !$this->canAccessStudent($contextStudent)) {
                $contextStudent = null;
            }
        }

        return view('ai.analytics', compact(
            'schoolAnalytics',
            'grades',
            'children',
            'selectedStudentId',
            'studentInsights',
            'contextStudent'
        ));
    }

    /**
     * عرض واجهة المساعد الذكي
     */
    public function assistant()
    {
        $user = auth()->user();
        $role = $this->getRoleSlug($user);

        $children = collect();
        $contextStudent = null;

        if ($user->isParent()) {
            $children = $user->guardian?->students()->get() ?? collect();
        }

        return view('ai.assistant', compact('role', 'children', 'contextStudent'));
    }

    /**
     * معالجة سؤال المساعد (AJAX)
     */
    public function ask(Request $request)
    {
        $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'student_id' => ['nullable', 'exists:students,id'],
        ]);

        $user = auth()->user();
        $contextStudent = null;

        if ($request->filled('student_id')) {
            $student = Student::find($request->student_id);
            if ($student && $this->canAccessStudent($student)) {
                $contextStudent = $student;
            }
        }

        $role = $this->getRoleSlug($user);
        $response = $this->ai->assistant($request->question, $role, $contextStudent);

        return response()->json($response);
    }

    /**
     * الحصول على رؤى طالب (AJAX)
     */
    public function studentInsights(Request $request, Student $student)
    {
        $this->authorizeStudentAccess($student);

        return response()->json($this->ai->studentInsights($student));
    }

    /**
     * الحصول على تحليلات المدرسة (AJAX)
     */
    public function schoolAnalytics()
    {
        return response()->json($this->ai->schoolAnalytics());
    }

    /**
     * التحقق من صلاحية الوصول للطالب
     */
    protected function canAccessStudent(Student $student): bool
    {
        $user = auth()->user();

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            // المعلم له صلاحية على طلاب فصوله
            return $user->teacher?->classrooms()->where('classrooms.id', $student->classroom_id)->exists() ?? false;
        }

        if ($user->isParent()) {
            return $user->guardian?->students()->whereKey($student->id)->exists() ?? false;
        }

        return false;
    }

    /**
     * التحقق من الصلاحية (إعادة توجيه للحالة)
     */
    protected function authorizeStudentAccess(Student $student): void
    {
        abort_unless($this->canAccessStudent($student), 403);
    }

    /**
     * الحصول على slug الدور الحالي
     */
    protected function getRoleSlug($user): string
    {
        if ($user->isSuperAdmin()) return 'superadmin';
        if ($user->isAdmin()) return 'admin';
        if ($user->isTeacher()) return 'teacher';
        if ($user->isParent()) return 'parent';
        return 'guest';
    }
}
