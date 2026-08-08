<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\School;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Guardian;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AccountsSeeder extends Seeder
{
    /**
     * إنشاء الأدوار والمدرسة والصفوف والمواد والفصول والحسابات التجريبية
     */
    public function run(): void
    {
        echo "🚀 بدء إضافة الحسابات...\n\n";

        // ==========================================
        // 1. إنشاء الأدوار
        // ==========================================
        $roles = [
            ['name' => 'مدير المدرسة', 'slug' => 'admin'],
            ['name' => 'معلم', 'slug' => 'teacher'],
            ['name' => 'ولي أمر', 'slug' => 'parent'],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(['slug' => $roleData['slug']], $roleData);
            echo "✅ دور: {$role->name} (slug: {$role->slug})\n";
        }

        $adminRole = Role::where('slug', 'admin')->first();
        $teacherRole = Role::where('slug', 'teacher')->first();
        $parentRole = Role::where('slug', 'parent')->first();

        // ==========================================
        // 2. إنشاء مدرسة تجريبية
        // ==========================================
        $school = School::firstOrCreate(
            ['subdomain' => 'demo'],
            [
                'name' => 'مدرسة النور النموذجية',
                'name_en' => 'Al-Noor Model School',
                'email' => 'school@schoolpla.com',
                'phone' => '0500000000',
                'address' => 'الرياض - حي الملقا',
                'city' => 'الرياض',
                'country' => 'السعودية',
                'principal_name' => 'أ. محمد العلي',
                'type' => 'private',
                'level' => 'all',
                'max_students' => 500,
                'max_teachers' => 50,
                'is_active' => true,
                'is_verified' => true,
            ]
        );

        // إنشاء اشتراك مجاني للمدرسة
        if ($school->subscriptions()->count() === 0) {
            $school->subscriptions()->create([
                'plan' => 'free',
                'price' => 0,
                'starts_at' => now(),
                'ends_at' => now()->addYear(),
                'is_active' => true,
            ]);
        }

        echo "✅ مدرسة: {$school->name} (subdomain: {$school->subdomain})\n";

        // ==========================================
        // 3. إنشاء الصفوف والمواد والفصول
        // ==========================================
        $gradeNames = ['الصف الأول الابتدائي', 'الصف الثاني الابتدائي', 'الصف الثالث الابتدائي'];
        $grades = [];

        foreach ($gradeNames as $i => $name) {
            $grade = Grade::firstOrCreate(
                ['school_id' => $school->id, 'name' => $name],
                ['order' => $i + 1, 'level' => 1]
            );
            $grades[] = $grade;
        }
        echo "✅ الصفوف: " . count($grades) . " صفوف\n";

        $subjectNames = [
            ['name' => 'اللغة العربية', 'code' => 'AR101', 'color' => '#ef4444'],
            ['name' => 'الرياضيات', 'code' => 'MATH101', 'color' => '#3b82f6'],
            ['name' => 'العلوم', 'code' => 'SCI101', 'color' => '#22c55e'],
            ['name' => 'اللغة الإنجليزية', 'code' => 'EN101', 'color' => '#8b5cf6'],
        ];

        $subjects = [];
        foreach ($subjectNames as $subjectData) {
            $subject = Subject::firstOrCreate(
                ['school_id' => $school->id, 'code' => $subjectData['code']],
                [
                    'name' => $subjectData['name'],
                    'color' => $subjectData['color'],
                    'description' => "مادة {$subjectData['name']}",
                ]
            );
            $subjects[] = $subject;
        }
        echo "✅ المواد: " . count($subjects) . " مواد\n";

        // ربط المواد بالصفوف
        foreach ($grades as $grade) {
            if ($grade->subjects()->count() === 0) {
                $grade->subjects()->attach(collect($subjects)->pluck('id')->toArray());
            }
        }
        echo "✅ تم ربط المواد بالصفوف\n";

        // إنشاء فصول
        $classrooms = [];
        foreach ($grades as $grade) {
            $classroom = Classroom::firstOrCreate(
                ['school_id' => $school->id, 'grade_id' => $grade->id, 'name' => 'فصل أ'],
                ['capacity' => 30]
            );
            $classrooms[] = $classroom;
        }
        echo "✅ الفصول: " . count($classrooms) . " فصول\n";

        // ==========================================
        // 4. إنشاء الحسابات
        // ==========================================
        $this->createOrUpdateSuperAdmin();
        $this->createAdmin($school, $adminRole);
        $this->createTeachers($school, $teacherRole, $subjects, $classrooms);
        $this->createParents($school, $parentRole);
        $this->createStudents($school, $classrooms);

        echo "\n========================================\n";
        echo "🎉 تم إضافة جميع الحسابات بنجاح!\n\n";
        echo "📋 ملخص الحسابات:\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "🔑 Super Admin  | الهاتف: 0500000001 | كلمة المرور: password\n";
        echo "🔑 Admin        | الهاتف: 0500000002 | كلمة المرور: password\n";
        echo "🔑 معلم 1       | الهاتف: 0500000003 | كلمة المرور: password\n";
        echo "🔑 معلم 2       | الهاتف: 0500000004 | كلمة المرور: password\n";
        echo "🔑 معلم 3       | الهاتف: 0500000005 | كلمة المرور: password\n";
        echo "🔑 ولي أمر 1    | الهاتف: 0500000006 | كلمة المرور: password\n";
        echo "🔑 ولي أمر 2    | الهاتف: 0500000007 | كلمة المرور: password\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "💡 جميع الحسابات بكلمة مرور موحدة: password\n";
        echo "🏫 مدرسة تجريبية: {$school->name} (subdomain: demo)\n";
    }

    private function createOrUpdateSuperAdmin(): void
    {
        $superAdmin = User::where('is_super_admin', true)->first();
        if ($superAdmin) {
            echo "ℹ️ Super Admin موجود بالفعل: {$superAdmin->name}\n";
            return;
        }

        User::create([
            'name' => 'المدير العام',
            'email' => 'superadmin@schoolpla.com',
            'phone' => '0500000001',
            'password' => Hash::make('password'),
            'is_super_admin' => true,
            'is_active' => true,
        ]);
        echo "✅ Super Admin: المدير العام | الهاتف: 0500000001 | كلمة المرور: password\n";
    }

    private function createAdmin(School $school, Role $adminRole): void
    {
        $user = User::where('phone', '0500000002')->first();
        if ($user) {
            echo "ℹ️ Admin موجود بالفعل: {$user->name}\n";
            return;
        }

        User::create([
            'name' => 'مدير المدرسة',
            'email' => 'admin@schoolpla.com',
            'phone' => '0500000002',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'school_id' => $school->id,
            'is_active' => true,
        ]);
        echo "✅ Admin: مدير المدرسة | الهاتف: 0500000002 | كلمة المرور: password\n";
    }

    private function createTeachers(School $school, Role $teacherRole, array $subjects, array $classrooms): void
    {
        $teachers = [
            ['name' => 'أ. أحمد محمد', 'phone' => '0500000003'],
            ['name' => 'أ. خالد عبدالله', 'phone' => '0500000004'],
            ['name' => 'أ. سارة علي', 'phone' => '0500000005'],
        ];

        foreach ($teachers as $key => $data) {
            $user = User::where('phone', $data['phone'])->first();
            if ($user) {
                echo "ℹ️ معلم موجود بالفعل: {$user->name}\n";
                continue;
            }

            $user = User::create([
                'name' => $data['name'],
                'email' => 'teacher' . ($key + 1) . '@schoolpla.com',
                'phone' => $data['phone'],
                'password' => Hash::make('password'),
                'role_id' => $teacherRole->id,
                'school_id' => $school->id,
                'is_active' => true,
            ]);

            $teacher = Teacher::create([
                'user_id' => $user->id,
                'school_id' => $school->id,
                'phone' => $data['phone'],
                'specialization' => $subjects[$key % count($subjects)]->name,
                'hire_date' => now()->subYears(rand(1, 5)),
            ]);

            // ربط المعلم بمادة
            $teacher->subjects()->sync([$subjects[$key % count($subjects)]->id]);

            // ربط المعلم بفصل ومادة (pivot)
            $classroom = $classrooms[$key % count($classrooms)];
            $teacher->classrooms()->syncWithoutDetaching([
                $classroom->id => [
                    'subject_id' => $subjects[$key % count($subjects)]->id,
                ]
            ]);

            echo "✅ معلم: {$user->name} | الهاتف: {$data['phone']} | كلمة المرور: password\n";
        }
    }

    private function createParents(School $school, Role $parentRole): void
    {
        $parents = [
            ['name' => 'والد الطالب 1', 'phone' => '0500000006'],
            ['name' => 'والد الطالب 2', 'phone' => '0500000007'],
        ];

        foreach ($parents as $key => $data) {
            $user = User::where('phone', $data['phone'])->first();
            if ($user) {
                echo "ℹ️ ولي أمر موجود بالفعل: {$user->name}\n";
                continue;
            }

            $user = User::create([
                'name' => $data['name'],
                'email' => 'parent' . ($key + 1) . '@schoolpla.com',
                'phone' => $data['phone'],
                'password' => Hash::make('password'),
                'role_id' => $parentRole->id,
                'school_id' => $school->id,
                'is_active' => true,
            ]);

            Guardian::create([
                'user_id' => $user->id,
                'school_id' => $school->id,
                'phone' => $data['phone'],
                'relationship' => 'father',
            ]);

            echo "✅ ولي أمر: {$user->name} | الهاتف: {$data['phone']} | كلمة المرور: password\n";
        }
    }

    private function createStudents(School $school, array $classrooms): void
    {
        $guardians = Guardian::where('school_id', $school->id)->get();
        $students = [
            ['name' => 'محمد أحمد', 'gender' => 'male'],
            ['name' => 'فاطمة خالد', 'gender' => 'female'],
        ];

        foreach ($students as $key => $data) {
            $guardian = $guardians[$key % count($guardians)];
            $classroom = $classrooms[$key % count($classrooms)];

            Student::firstOrCreate(
                ['school_id' => $school->id, 'student_id' => 'STU-2024-' . str_pad($key + 1, 5, '0')],
                [
                    'name' => $data['name'],
                    'gender' => $data['gender'],
                    'birth_date' => now()->subYears(rand(6, 12)),
                    'grade_id' => $classroom->grade_id,
                    'classroom_id' => $classroom->id,
                    'guardian_id' => $guardian->id,
                    'is_active' => true,
                ]
            );

            echo "✅ طالب: {$data['name']}\n";
        }
    }
}
