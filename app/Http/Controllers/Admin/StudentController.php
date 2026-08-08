<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    /**
     * الصفحة الرئيسية - عرض الصفوف كقوالب
     */
    public function index()
    {
        $grades = Grade::withCount(['classrooms'])
            ->with(['classrooms' => function ($q) {
                $q->withCount('students');
            }])
            ->orderBy('order')
            ->get();

        // إضافة عدد الطلاب لكل صف
        $grades->each(function ($grade) {
            $grade->students_count = Student::where('grade_id', $grade->id)->count();
        });

        $totalStudents = Student::count();
        $maleStudents = Student::where('gender', 'male')->count();
        $femaleStudents = Student::where('gender', 'female')->count();
        $studentsWithoutClassroom = Student::whereNull('classroom_id')->count();

        return view('admin.students.index', compact(
            'grades', 'totalStudents', 'maleStudents', 'femaleStudents', 'studentsWithoutClassroom'
        ));
    }

    /**
     * عرض طلاب صف معين
     */
    public function byGrade(Request $request, Grade $grade)
    {
        $query = Student::with(['classroom', 'guardian.user'])
            ->where('grade_id', $grade->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }

        $students = $query->latest()->paginate(15);
        $classrooms = $grade->classrooms;
        $guardians = Guardian::with('user')->get();

        return view('admin.students.grade', compact('grade', 'students', 'classrooms', 'guardians'));
    }

    public function create()
    {
        // توجيه المستخدم لاختيار الصف أولاً بدلاً من الإضافة المباشرة
        return redirect()->route('admin.students.index')
            ->with('info', 'لإضافة طالب جديد، يرجى اختيار الصف الدراسي أولاً ثم الضغط عليه.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female'],
            'birth_date' => ['required', 'date', 'before:today'],
            'grade_id' => ['required', 'exists:grades,id'],
            'classroom_id' => ['nullable', 'exists:classrooms,id'],
            'guardian_id' => ['required', 'exists:guardians,id'],
            'address' => ['nullable', 'string', 'max:500'],
            'medical_notes' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $validated['student_id'] = 'STU-' . date('Y') . '-' . Str::padLeft(Student::count() + 1, 5, '0');
        $validated['school_id'] = auth()->user()->school_id;

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        $student = Student::create($validated);

        ActivityLog::log('create_student', "إضافة طالب جديد: {$student->name}", $student);

        // العودة للصف إذا كان الطلب من صفحة الصف
        if ($request->filled('redirect_to_grade')) {
            return redirect()->route('admin.students.grade', $validated['grade_id'])
                ->with('success', 'تم إضافة الطالب بنجاح.');
        }

        return redirect()->route('admin.students.index')
            ->with('success', 'تم إضافة الطالب بنجاح.');
    }

    /**
     * إضافة طالب من صفحة الصف
     */
    public function storeInGrade(Request $request, Grade $grade)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female'],
            'birth_date' => ['required', 'date', 'before:today'],
            'guardian_id' => ['required', 'exists:guardians,id'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $validated['grade_id'] = $grade->id;
        $validated['classroom_id'] = null; // بدون فصل مبدئياً
        $validated['student_id'] = 'STU-' . date('Y') . '-' . Str::padLeft(Student::count() + 1, 5, '0');
        $validated['school_id'] = auth()->user()->school_id;

        // رفع الصورة إن وجدت
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        $student = Student::create($validated);

        ActivityLog::log('create_student', "إضافة طالب جديد: {$student->name} للصف {$grade->name}", $student);

        return redirect()->route('admin.students.grade', $grade)
            ->with('success', 'تم إضافة الطالب بنجاح.');
    }

    /**
     * إضافة ولي أمر سريع (AJAX)
     */
    public function storeGuardianQuick(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6'],
            'relation' => ['required', 'in:father,mother,other'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'emergency_phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        // إنشاء مستخدم جديد
        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['phone'] . '@temp.edulink.com',
            'phone' => $validated['phone'],
            'password' => bcrypt($validated['password']),
            'role_id' => \App\Models\Role::where('name', 'parent')->first()->id ?? 4,
            'school_id' => auth()->user()->school_id,
            'is_active' => true,
        ]);

        // إنشاء ولي الأمر
        $guardian = Guardian::create([
            'user_id' => $user->id,
            'school_id' => auth()->user()->school_id,
            'relation' => $validated['relation'],
            'phone' => $validated['phone'],
            'occupation' => $validated['occupation'] ?? null,
            'emergency_phone' => $validated['emergency_phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'guardian' => [
                'id' => $guardian->id,
                'name' => $user->name,
            ]
        ]);
    }

    public function show(Student $student)
    {
        $student->load([
            'grade',
            'classroom',
            'guardian.user',
            'attendances' => fn($q) => $q->latest()->take(30),
            'scores.subject',
            'behaviors.teacher.user',
        ]);

        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $grades = Grade::with('classrooms')->get();
        $guardians = Guardian::with('user')->get();

        return view('admin.students.edit', compact('student', 'grades', 'guardians'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female'],
            'birth_date' => ['required', 'date', 'before:today'],
            'grade_id' => ['required', 'exists:grades,id'],
            'classroom_id' => ['nullable', 'exists:classrooms,id'],
            'guardian_id' => ['required', 'exists:guardians,id'],
            'address' => ['nullable', 'string', 'max:500'],
            'medical_notes' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        $student->update($validated);

        ActivityLog::log('update_student', "تحديث بيانات الطالب: {$student->name}", $student);

        // العودة للصف إذا كان الطلب من صفحة الصف
        if ($request->filled('redirect_to_grade')) {
            return redirect()->route('admin.students.grade', $validated['grade_id'])
                ->with('success', 'تم تحديث بيانات الطالب بنجاح.');
        }

        return redirect()->route('admin.students.index')
            ->with('success', 'تم تحديث بيانات الطالب بنجاح.');
    }

    public function destroy(Student $student)
    {
        $name = $student->name;
        $gradeId = $student->grade_id;
        $student->delete(); // Soft delete

        ActivityLog::log('delete_student', "حذف الطالب: {$name}");

        if (request()->filled('redirect_to_grade') && $gradeId) {
            return redirect()->route('admin.students.grade', $gradeId)
                ->with('success', 'تم حذف الطالب بنجاح (يمكن استعادته من سلة المحذوفات).');
        }

        return redirect()->route('admin.students.index')
            ->with('success', 'تم حذف الطالب بنجاح (يمكن استعادته من سلة المحذوفات).');
    }

    /**
     * عرض سلة المحذوفات
     */
    public function trash()
    {
        $students = Student::onlyTrashed()
            ->with(['grade', 'classroom', 'guardian.user'])
            ->latest('deleted_at')
            ->paginate(15);

        return view('admin.students.trash', compact('students'));
    }

    /**
     * استعادة طالب محذوف
     */
    public function restore($id)
    {
        $student = Student::onlyTrashed()->findOrFail($id);
        $student->restore();

        ActivityLog::log('restore_student', "استعادة الطالب: {$student->name}", $student);

        return redirect()->back()
            ->with('success', "تم استعادة الطالب {$student->name} بنجاح.");
    }

    /**
     * حذف نهائي للطالب
     */
    public function forceDelete($id)
    {
        $student = Student::onlyTrashed()->findOrFail($id);
        $name = $student->name;
        $student->forceDelete();

        ActivityLog::log('force_delete_student', "حذف الطالب نهائياً: {$name}");

        return redirect()->back()
            ->with('success', "تم حذف الطالب {$name} نهائياً.");
    }

    /**
     * تعيين الفصل للطلاب (من صفحة الفصول)
     */
    public function assignClassroom(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => ['required', 'array'],
            'student_ids.*' => ['exists:students,id'],
            'classroom_id' => ['required', 'exists:classrooms,id'],
        ]);

        Student::whereIn('id', $validated['student_ids'])
            ->update(['classroom_id' => $validated['classroom_id']]);

        return redirect()->back()
            ->with('success', 'تم تعيين الفصل للطلاب بنجاح.');
    }

    /**
     * إزالة الطالب من الفصل (إبقاؤه في الصف فقط)
     */
    public function removeFromClassroom(Student $student)
    {
        $student->update(['classroom_id' => null]);

        return redirect()->back()
            ->with('success', "تم إزالة الطالب {$student->name} من الفصل.");
    }
}
