<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Grade;
use App\Models\Notification;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolSubscription;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * خدمة تسجيل المدارس
 * تتعامل مع كامل منطق تسجيل مدرسة جديدة مع طبقات أمان متعددة
 */
class SchoolRegistrationService
{
    /**
     * مدة صلاحية رمز التحقق بالدقائق
     */
    protected int $verificationTtl = 30;

    /**
     * عدد محاولات رمز التحقق المسموحة
     */
    protected int $maxVerificationAttempts = 5;

    /**
     * تسجيل مدرسة جديدة كاملة
     */
    public function register(array $data, Request $request): School
    {
        DB::beginTransaction();

        try {
            // 1. إنشاء المدرسة (غير مفعّلة - تنتظر موافقة الإدارة)
            $school = School::create([
                'name' => $data['school_name'],
                'subdomain' => strtolower($data['subdomain']),
                'type' => $data['type'],
                'level' => $data['level'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'address' => $data['address'] ?? null,
                'is_active' => false,
                'is_verified' => false,
                // معلومات التسجيل
                'registration_ip' => $request->ip(),
                'registration_user_agent' => substr($request->userAgent(), 0, 500),
                'registration_completed_at' => now(),
            ]);

            // 2. الحصول على دور المدير
            $adminRole = Role::where('slug', 'admin')->first();
            if (!$adminRole) {
                $adminRole = Role::create([
                    'name' => 'مدير',
                    'slug' => 'admin',
                    'description' => 'مدير المدرسة',
                ]);
            }

            // 3. إنشاء حساب المدير
            $admin = User::create([
                'name' => $data['admin_name'],
                'phone' => $data['admin_phone'],
                'password' => Hash::make($data['admin_password']),
                'role_id' => $adminRole->id,
                'school_id' => $school->id,
                'is_active' => true,
                'is_super_admin' => false,
            ]);

            // 4. إنشاء اشتراك مجاني تجريبي (30 يوم)
            SchoolSubscription::create([
                'school_id' => $school->id,
                'plan' => 'free',
                'starts_at' => now(),
                'ends_at' => now()->addDays(30),
                'is_active' => true,
            ]);

            // 5. إنشاء الصفوف والمواد الافتراضية
            $this->createDefaultGrades($school);
            $this->createDefaultSubjects($school);

            // 6. تسجيل النشاط
            ActivityLog::log(
                action: 'school_registered',
                description: "تسجيل مدرسة جديدة: {$school->name}",
                model: $school,
                userId: $admin->id,
                newValues: [
                    'school_id' => $school->id,
                    'subdomain' => $school->subdomain,
                    'admin_phone' => $admin->phone,
                    'ip' => $request->ip(),
                ]
            );

            // 7. إشعار جميع الـ Super Admins
            $this->notifySuperAdmins($school);

            DB::commit();

            return $school;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * توليد رمز تحقق للمدرسة
     */
    public function generateVerificationCode(int $schoolId): string
    {
        $code = (string) random_int(100000, 999999);

        School::where('id', $schoolId)->update([
            'verification_code' => Hash::make($code),
            'verification_expires_at' => now()->addMinutes($this->verificationTtl),
        ]);

        return $code;
    }

    /**
     * التحقق من رمز التحقق
     */
    public function verifyCode(int $schoolId, string $code): bool
    {
        $school = School::findOrFail($schoolId);

        // التحقق من انتهاء الصلاحية
        if (!$school->verification_expires_at || $school->verification_expires_at->isPast()) {
            return false;
        }

        // التحقق من الرمز
        if (!$school->verification_code || !Hash::check($code, $school->verification_code)) {
            return false;
        }

        // تفعيل التحقق
        $school->update([
            'verification_code' => null,
            'verification_expires_at' => null,
            'verified_at' => now(),
        ]);

        return true;
    }

    /**
     * إشعار جميع المشرفين العامين عن مدرسة جديدة
     */
    protected function notifySuperAdmins(School $school): void
    {
        $superAdmins = User::where('is_super_admin', true)->get();

        foreach ($superAdmins as $admin) {
            Notification::create([
                'school_id' => null,
                'user_id' => $admin->id,
                'title' => 'طلب تسجيل مدرسة جديدة',
                'message' => "المدرسة {$school->name} سجلت طلباً جديداً. يرجى مراجعته.",
                'type' => 'warning',
                'action_url' => route('superadmin.schools.show', $school),
                'action_text' => 'مراجعة الطلب',
            ]);
        }
    }

    /**
     * إنشاء الصفوف الافتراضية حسب المرحلة
     */
    public function createDefaultGrades(School $school): void
    {
        $grades = [];

        if (in_array($school->level, ['elementary', 'all'])) {
            $grades = array_merge($grades, [
                ['name' => 'الصف الأول الابتدائي', 'level' => 1, 'order' => 1],
                ['name' => 'الصف الثاني الابتدائي', 'level' => 2, 'order' => 2],
                ['name' => 'الصف الثالث الابتدائي', 'level' => 3, 'order' => 3],
                ['name' => 'الصف الرابع الابتدائي', 'level' => 4, 'order' => 4],
                ['name' => 'الصف الخامس الابتدائي', 'level' => 5, 'order' => 5],
                ['name' => 'الصف السادس الابتدائي', 'level' => 6, 'order' => 6],
            ]);
        }

        if (in_array($school->level, ['middle', 'all'])) {
            $grades = array_merge($grades, [
                ['name' => 'الصف الأول المتوسط', 'level' => 7, 'order' => 7],
                ['name' => 'الصف الثاني المتوسط', 'level' => 8, 'order' => 8],
                ['name' => 'الصف الثالث المتوسط', 'level' => 9, 'order' => 9],
            ]);
        }

        if (in_array($school->level, ['high', 'all'])) {
            $grades = array_merge($grades, [
                ['name' => 'الصف الأول الثانوي', 'level' => 10, 'order' => 10],
                ['name' => 'الصف الثاني الثانوي', 'level' => 11, 'order' => 11],
                ['name' => 'الصف الثالث الثانوي', 'level' => 12, 'order' => 12],
            ]);
        }

        foreach ($grades as $grade) {
            Grade::create([
                'name' => $grade['name'],
                'level' => $grade['level'],
                'order' => $grade['order'],
                'school_id' => $school->id,
            ]);
        }
    }

    /**
     * إنشاء المواد الافتراضية
     */
    public function createDefaultSubjects(School $school): void
    {
        $subjects = [
            ['name' => 'اللغة العربية', 'code' => 'AR101', 'color' => '#ef4444'],
            ['name' => 'الرياضيات', 'code' => 'MATH101', 'color' => '#3b82f6'],
            ['name' => 'العلوم', 'code' => 'SCI101', 'color' => '#22c55e'],
            ['name' => 'اللغة الإنجليزية', 'code' => 'EN101', 'color' => '#8b5cf6'],
            ['name' => 'التربية الإسلامية', 'code' => 'ISL101', 'color' => '#14b8a6'],
            ['name' => 'الدراسات الاجتماعية', 'code' => 'SOC101', 'color' => '#f59e0b'],
            ['name' => 'التربية الفنية', 'code' => 'ART101', 'color' => '#ec4899'],
            ['name' => 'التربية البدنية', 'code' => 'PE101', 'color' => '#10b981'],
        ];

        foreach ($subjects as $subject) {
            Subject::create([
                'name' => $subject['name'],
                'code' => $subject['code'],
                'color' => $subject['color'],
                'school_id' => $school->id,
            ]);
        }
    }
}
