<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolSubscription;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SchoolRegisterController extends Controller
{
    /**
     * عرض نموذج تسجيل المدرسة
     */
    public function showForm()
    {
        return view('auth.school-register');
    }

    /**
     * تسجيل مدرسة جديدة
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'school_name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:50|unique:schools,subdomain|regex:/^[a-z0-9-]+$/',
            'type' => 'required|in:public,private,international',
            'level' => 'required|in:elementary,middle,high,all',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'admin_name' => 'required|string|max:255',
            'admin_phone' => 'required|string|max:20|unique:users,phone',
            'admin_password' => 'required|string|min:8|confirmed',
        ], [
            'subdomain.regex' => 'النطاق الفرعي يجب أن يحتوي على أحرف إنجليزية صغيرة وأرقام فقط',
            'subdomain.unique' => 'هذا النطاق الفرعي مستخدم بالفعل',
            'admin_phone.unique' => 'رقم الجوال مسجل مسبقاً',
        ]);

        DB::beginTransaction();

        try {
            // إنشاء المدرسة
            $school = School::create([
                'name' => $validated['school_name'],
                'subdomain' => $validated['subdomain'],
                'type' => $validated['type'],
                'level' => $validated['level'],
                'phone' => $validated['phone'],
                'email' => $validated['email'] ?? null,
                'address' => $validated['address'] ?? null,
                'is_active' => false, // ستحتاج موافقة
                'is_verified' => false,
            ]);

            // الحصول على دور المدير
            $adminRole = Role::where('slug', 'admin')->first();
            if (!$adminRole) {
                $adminRole = Role::create([
                    'name' => 'مدير',
                    'slug' => 'admin',
                    'description' => 'مدير المدرسة',
                ]);
            }

            // إنشاء حساب المدير
            $admin = User::create([
                'name' => $validated['admin_name'],
                'phone' => $validated['admin_phone'],
                'password' => Hash::make($validated['admin_password']),
                'role_id' => $adminRole->id,
                'school_id' => $school->id,
                'is_active' => true,
                'is_super_admin' => false,
            ]);

            // إنشاء اشتراك مجاني تجريبي
            SchoolSubscription::create([
                'school_id' => $school->id,
                'plan' => 'free',
                'starts_at' => now(),
                'ends_at' => now()->addDays(30), // تجريبي 30 يوم
                'is_active' => true,
            ]);

            // إنشاء الصفوف الافتراضية حسب المرحلة
            $this->createDefaultGrades($school);

            // إنشاء المواد الافتراضية
            $this->createDefaultSubjects($school);

            DB::commit();

            return redirect()->route('school.register.success')
                ->with('school_name', $school->name)
                ->with('subdomain', $school->subdomain);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'حدث خطأ أثناء التسجيل: ' . $e->getMessage()]);
        }
    }

    /**
     * صفحة نجاح التسجيل
     */
    public function success()
    {
        if (!session('school_name')) {
            return redirect()->route('school.register');
        }

        return view('auth.school-register-success');
    }

    /**
     * إنشاء الصفوف الافتراضية
     */
    protected function createDefaultGrades(School $school): void
    {
        $grades = [];

        if (in_array($school->level, ['elementary', 'all'])) {
            $grades = array_merge($grades, [
                ['name' => 'الصف الأول الابتدائي', 'level' => 1],
                ['name' => 'الصف الثاني الابتدائي', 'level' => 2],
                ['name' => 'الصف الثالث الابتدائي', 'level' => 3],
                ['name' => 'الصف الرابع الابتدائي', 'level' => 4],
                ['name' => 'الصف الخامس الابتدائي', 'level' => 5],
                ['name' => 'الصف السادس الابتدائي', 'level' => 6],
            ]);
        }

        if (in_array($school->level, ['middle', 'all'])) {
            $grades = array_merge($grades, [
                ['name' => 'الصف الأول المتوسط', 'level' => 7],
                ['name' => 'الصف الثاني المتوسط', 'level' => 8],
                ['name' => 'الصف الثالث المتوسط', 'level' => 9],
            ]);
        }

        if (in_array($school->level, ['high', 'all'])) {
            $grades = array_merge($grades, [
                ['name' => 'الصف الأول الثانوي', 'level' => 10],
                ['name' => 'الصف الثاني الثانوي', 'level' => 11],
                ['name' => 'الصف الثالث الثانوي', 'level' => 12],
            ]);
        }

        foreach ($grades as $grade) {
            Grade::create([
                'name' => $grade['name'],
                'level' => $grade['level'],
                'school_id' => $school->id,
            ]);
        }
    }

    /**
     * إنشاء المواد الافتراضية
     */
    protected function createDefaultSubjects(School $school): void
    {
        $subjects = [
            'اللغة العربية',
            'الرياضيات',
            'العلوم',
            'اللغة الإنجليزية',
            'التربية الإسلامية',
            'الدراسات الاجتماعية',
            'التربية الفنية',
            'التربية البدنية',
        ];

        foreach ($subjects as $subject) {
            Subject::create([
                'name' => $subject,
                'school_id' => $school->id,
            ]);
        }
    }

    /**
     * التحقق من توفر النطاق الفرعي (AJAX)
     */
    public function checkSubdomain(Request $request)
    {
        $subdomain = $request->input('subdomain');
        
        if (empty($subdomain)) {
            return response()->json(['available' => false, 'message' => 'النطاق الفرعي مطلوب']);
        }

        // التحقق من الصيغة
        if (!preg_match('/^[a-z0-9-]+$/', $subdomain)) {
            return response()->json(['available' => false, 'message' => 'النطاق يجب أن يحتوي على أحرف إنجليزية صغيرة وأرقام فقط']);
        }

        // التحقق من الطول
        if (strlen($subdomain) < 3 || strlen($subdomain) > 50) {
            return response()->json(['available' => false, 'message' => 'النطاق يجب أن يكون بين 3 و 50 حرفاً']);
        }

        // التحقق من الكلمات المحجوزة
        $reserved = ['admin', 'superadmin', 'api', 'www', 'mail', 'ftp', 'support', 'help', 'login', 'register', 'app', 'dashboard'];
        if (in_array($subdomain, $reserved)) {
            return response()->json(['available' => false, 'message' => 'هذا النطاق محجوز']);
        }

        // التحقق من الاستخدام
        $exists = School::where('subdomain', $subdomain)->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'هذا النطاق مستخدم بالفعل' : 'النطاق متاح ✓'
        ]);
    }
}
