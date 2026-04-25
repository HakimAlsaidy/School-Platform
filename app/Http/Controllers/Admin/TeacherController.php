<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = Teacher::with(['user', 'subjects', 'classrooms.grade']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('subject_id')) {
            $query->whereHas('subjects', function ($q) use ($request) {
                $q->where('subjects.id', $request->subject_id);
            });
        }

        $teachers = $query->latest()->paginate(15);
        $subjects = Subject::all();

        return view('admin.teachers.index', compact('teachers', 'subjects'));
    }

    public function create()
    {
        $subjects = Subject::all();
        $classrooms = \App\Models\Classroom::with('grade')->get();
        $grades = \App\Models\Grade::with('classrooms')->get();

        return view('admin.teachers.create', compact('subjects', 'classrooms', 'grades'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'min:8', 'confirmed'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'hire_date' => ['nullable', 'date'],
            'qualifications' => ['nullable', 'string', 'max:1000'],
            'assignments' => ['nullable', 'array'],
            'assignments.*.grade_id' => ['nullable', 'exists:grades,id'],
            'assignments.*.subjects' => ['nullable', 'array'],
            'assignments.*.subjects.*' => ['exists:subjects,id'],
        ]);

        $roleId = Role::where('slug', Role::TEACHER)->first()?->id;
        $schoolId = auth()->user()->school_id;

        $user = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role_id' => $roleId,
            'school_id' => $schoolId,
            'is_active' => $request->has('is_active'),
        ]);

        $teacher = Teacher::create([
            'user_id' => $user->id,
            'school_id' => $schoolId,
            'phone' => $validated['phone'],
            'specialization' => $validated['specialization'] ?? null,
            'hire_date' => $validated['hire_date'] ?? null,
            'qualifications' => $validated['qualifications'] ?? null,
        ]);

        // جمع كل المواد المختارة
        $allSubjects = [];
        if (!empty($validated['assignments'])) {
            foreach ($validated['assignments'] as $assignment) {
                if (!empty($assignment['grade_id']) && !empty($assignment['subjects'])) {
                    $allSubjects = array_merge($allSubjects, $assignment['subjects']);
                }
            }
        }
        
        // حفظ المواد (المواد الفريدة فقط)
        if (!empty($allSubjects)) {
            $teacher->subjects()->sync(array_unique($allSubjects));
        }

        // حفظ تعيينات الصفوف والمواد
        if (!empty($validated['assignments'])) {
            foreach ($validated['assignments'] as $assignment) {
                if (!empty($assignment['grade_id']) && !empty($assignment['subjects'])) {
                    // إذا كان هناك جدول pivot للصفوف والمواد مع المعلم
                    // يمكن حفظه هنا حسب هيكل قاعدة البيانات
                    // حالياً سنحفظ المواد فقط
                }
            }
        }

        ActivityLog::log('create_teacher', "إضافة معلم جديد: {$user->name}", $teacher);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'تم إضافة المعلم بنجاح.');
    }

    public function show(Teacher $teacher)
    {
        $teacher->load([
            'user',
            'subjects',
            'classrooms.grade',
            'scores' => fn($q) => $q->latest()->take(20),
            'schedules.classroom.grade',
        ]);

        return view('admin.teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        $subjects = Subject::all();
        $classrooms = \App\Models\Classroom::with('grade')->get();
        $grades = \App\Models\Grade::with('classrooms')->get();
        $teacher->load(['subjects', 'classrooms']);

        return view('admin.teachers.edit', compact('teacher', 'subjects', 'classrooms', 'grades'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone,' . $teacher->user_id],
            'password' => ['nullable', 'min:8'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'assignments' => ['nullable', 'array'],
            'assignments.*.grade_id' => ['nullable', 'exists:grades,id'],
            'assignments.*.subjects' => ['nullable', 'array'],
            'assignments.*.subjects.*' => ['exists:subjects,id'],
            'is_active' => ['nullable'],
        ]);

        $teacher->user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'is_active' => $request->has('is_active'),
        ]);

        if (!empty($validated['password'])) {
            $teacher->user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        $teacher->update([
            'phone' => $validated['phone'],
            'specialization' => $validated['specialization'] ?? null,
        ]);

        // جمع كل المواد المختارة
        $allSubjects = [];
        if (!empty($validated['assignments'])) {
            foreach ($validated['assignments'] as $assignment) {
                if (!empty($assignment['grade_id']) && !empty($assignment['subjects'])) {
                    $allSubjects = array_merge($allSubjects, $assignment['subjects']);
                }
            }
        }
        
        // حفظ المواد (المواد الفريدة فقط)
        $teacher->subjects()->sync(array_unique($allSubjects));

        ActivityLog::log('update_teacher', "تحديث بيانات المعلم: {$teacher->user->name}", $teacher);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'تم تحديث بيانات المعلم بنجاح.');
    }

    public function destroy(Teacher $teacher)
    {
        $name = $teacher->user->name;
        $teacher->user->delete();

        ActivityLog::log('delete_teacher', "حذف المعلم: {$name}");

        return redirect()->route('admin.teachers.index')
            ->with('success', 'تم حذف المعلم بنجاح.');
    }
}
