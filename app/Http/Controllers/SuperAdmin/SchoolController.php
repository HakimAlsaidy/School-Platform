<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Role;
use App\Models\User;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SchoolController extends Controller
{
    public function index(Request $request)
    {
        $query = School::withCount(['students', 'teachers', 'classrooms']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('subdomain', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true)->where('is_verified', true);
            } elseif ($request->status === 'pending') {
                $query->where(function($q) {
                    $q->where('is_active', false)->orWhere('is_verified', false);
                });
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $schools = $query->latest()->paginate(15);

        return view('superadmin.schools.index', compact('schools'));
    }

    public function create()
    {
        return view('superadmin.schools.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'subdomain' => ['required', 'string', 'max:50', 'unique:schools,subdomain', 'alpha_dash'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'principal_name' => ['nullable', 'string', 'max:255'],
            'principal_phone' => ['nullable', 'string', 'max:20'],
            'type' => ['required', 'in:public,private,international'],
            'level' => ['required', 'in:elementary,middle,high,all'],
            'max_students' => ['nullable', 'integer', 'min:1'],
            'max_teachers' => ['nullable', 'integer', 'min:1'],
            // بيانات مدير المدرسة
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'admin_password' => ['required', 'string', 'min:8'],
        ]);

        DB::beginTransaction();
        try {
            // إنشاء المدرسة
            $school = School::create([
                'name' => $validated['name'],
                'name_en' => $validated['name_en'] ?? null,
                'subdomain' => strtolower($validated['subdomain']),
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'principal_name' => $validated['principal_name'] ?? null,
                'principal_phone' => $validated['principal_phone'] ?? null,
                'type' => $validated['type'],
                'level' => $validated['level'],
                'max_students' => $validated['max_students'] ?? 500,
                'max_teachers' => $validated['max_teachers'] ?? 50,
                'is_active' => true,
                'is_verified' => true,
            ]);

            // إنشاء حساب مدير المدرسة
            $adminRole = Role::where('slug', 'admin')->first();
            User::create([
                'name' => $validated['admin_name'],
                'phone' => $validated['admin_phone'],
                'password' => Hash::make($validated['admin_password']),
                'role_id' => $adminRole->id,
                'school_id' => $school->id,
                'is_active' => true,
            ]);

            // إنشاء الصفوف الافتراضية
            $this->createDefaultGrades($school);

            // إنشاء المواد الافتراضية
            $this->createDefaultSubjects($school);

            // إنشاء اشتراك مجاني
            $school->subscriptions()->create([
                'plan' => 'free',
                'price' => 0,
                'starts_at' => now(),
                'ends_at' => now()->addYear(),
                'is_active' => true,
            ]);

            DB::commit();

            return redirect()->route('superadmin.schools.index')
                ->with('success', 'تم إنشاء المدرسة بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إنشاء المدرسة: ' . $e->getMessage());
        }
    }

    public function show(School $school)
    {
        $school->load(['subscriptions' => fn($q) => $q->latest()]);
        $stats = $school->getStats();

        return view('superadmin.schools.show', compact('school', 'stats'));
    }

    public function edit(School $school)
    {
        return view('superadmin.schools.edit', compact('school'));
    }

    public function update(Request $request, School $school)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'subdomain' => ['required', 'string', 'max:50', 'unique:schools,subdomain,' . $school->id, 'alpha_dash'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'principal_name' => ['nullable', 'string', 'max:255'],
            'principal_phone' => ['nullable', 'string', 'max:20'],
            'type' => ['required', 'in:public,private,international'],
            'level' => ['required', 'in:elementary,middle,high,all'],
            'max_students' => ['nullable', 'integer', 'min:1'],
            'max_teachers' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable'],
            'is_verified' => ['nullable'],
        ]);

        $school->update([
            ...$validated,
            'subdomain' => strtolower($validated['subdomain']),
            'is_active' => $request->has('is_active'),
            'is_verified' => $request->has('is_verified'),
        ]);

        return redirect()->route('superadmin.schools.index')
            ->with('success', 'تم تحديث بيانات المدرسة بنجاح');
    }

    public function destroy(School $school)
    {
        $school->delete();

        return redirect()->route('superadmin.schools.index')
            ->with('success', 'تم حذف المدرسة بنجاح');
    }

    public function approve(School $school)
    {
        $school->update([
            'is_active' => true,
            'is_verified' => true,
        ]);

        // TODO: إرسال إشعار للمدرسة

        return back()->with('success', 'تم تفعيل المدرسة بنجاح');
    }

    public function reject(School $school)
    {
        $school->update([
            'is_active' => false,
            'is_verified' => false,
        ]);

        return back()->with('success', 'تم رفض طلب المدرسة');
    }

    public function suspend(School $school)
    {
        $school->update(['is_active' => false]);

        return back()->with('success', 'تم تعليق المدرسة');
    }

    protected function createDefaultGrades(School $school): void
    {
        $grades = [
            ['name' => 'الصف الأول الابتدائي', 'order' => 1],
            ['name' => 'الصف الثاني الابتدائي', 'order' => 2],
            ['name' => 'الصف الثالث الابتدائي', 'order' => 3],
            ['name' => 'الصف الرابع الابتدائي', 'order' => 4],
            ['name' => 'الصف الخامس الابتدائي', 'order' => 5],
            ['name' => 'الصف السادس الابتدائي', 'order' => 6],
        ];

        foreach ($grades as $grade) {
            Grade::create([...$grade, 'school_id' => $school->id]);
        }
    }

    protected function createDefaultSubjects(School $school): void
    {
        $subjects = [
            ['name' => 'اللغة العربية', 'code' => 'AR101', 'color' => '#ef4444'],
            ['name' => 'الرياضيات', 'code' => 'MATH101', 'color' => '#3b82f6'],
            ['name' => 'العلوم', 'code' => 'SCI101', 'color' => '#22c55e'],
            ['name' => 'اللغة الإنجليزية', 'code' => 'EN101', 'color' => '#8b5cf6'],
            ['name' => 'التربية الإسلامية', 'code' => 'ISL101', 'color' => '#14b8a6'],
            ['name' => 'الدراسات الاجتماعية', 'code' => 'SOC101', 'color' => '#f59e0b'],
        ];

        foreach ($subjects as $subject) {
            Subject::create([...$subject, 'school_id' => $school->id]);
        }
    }
}
