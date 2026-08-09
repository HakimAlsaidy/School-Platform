<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Message;
use App\Models\Announcement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ExtraDataSeeder extends Seeder
{
    /**
     * إنشاء الرسائل والإعلانات والمستخدمين المعلّقين
     */
    public function run(): void
    {
        echo "🚀 بدء إضافة الرسائل والإعلانات...\n\n";

        $parentRole = Role::where('slug', 'parent')->first();
        $adminUser = User::where('phone', '0500000002')->first()
            ?? User::where('email', 'admin@schoolpla.com')->first();
$parentUsers = User::where('role_id', $parentRole ? $parentRole->id : 0)
            ->where('is_active', true)->get();
        $school = \App\Models\School::where('subdomain', 'demo')->first();
        $schoolId = $school ? $school->id : null;

        if (!$adminUser) {
            echo "❌ لا يوجد مستخدم Admin. شغّل AccountsSeeder أولاً.\n";
            return;
        }

        // ==========================================
        // 1. الرسائل
        // ==========================================
        $messageTemplates = [
            ['subject' => 'استفسار عن مستوى الطالب', 'content' => 'السلام عليكم، أود الاستفسار عن مستوى ابني الدراسي في مادة الرياضيات. هل يمكنكم إفادتي بذلك؟ شكراً لكم.'],
            ['subject' => 'طلب إذن غياب', 'content' => 'السلام عليكم، أود إعلامكم بأن ابني لن يتمكن من الحضور غداً بسبب موعد طبي. أرجو الموافقة على ذلك.'],
            ['subject' => 'شكر وتقدير', 'content' => 'أود أن أتقدم بالشكر الجزيل للمدرسة على جهودها المميزة في متابعة أبنائنا. بارك الله فيكم.'],
            ['subject' => 'استفسار عن الواجبات', 'content' => 'السلام عليكم، هل يمكن إرسال قائمة بالواجبات المطلوبة هذا الأسبوع؟ شكراً.'],
            ['subject' => 'ملاحظة على سلوك الطالب', 'content' => 'نود إعلامكم بأن الطالب أبدى سلوكاً إيجابياً في الفصل هذا الأسبوع.'],
        ];

        $index = 0;
        foreach ($parentUsers->take(5) as $parent) {
            Message::firstOrCreate(
                ['sender_id' => $parent->id, 'receiver_id' => $adminUser->id, 'subject' => $messageTemplates[$index % 5]['subject']],
                ['content' => $messageTemplates[$index % 5]['content'], 'is_read' => rand(0, 1) == 1]
            );
            $index++;
        }

        foreach ($parentUsers->take(3) as $parent) {
            Message::firstOrCreate(
                ['sender_id' => $adminUser->id, 'receiver_id' => $parent->id, 'subject' => 'رد: ' . $messageTemplates[array_rand($messageTemplates)]['subject']],
                ['content' => 'شكراً لتواصلكم. سيتم متابعة الموضوع والرد عليكم في أقرب وقت.', 'is_read' => false]
            );
        }

        echo "✅ الرسائل: " . Message::count() . "\n";

        // ==========================================
        // 2. الإعلانات
        // ==========================================
        $announcements = [
            ['title' => 'مرحباً بكم في العام الدراسي الجديد', 'content' => 'نرحب بجميع الطلاب والمعلمين وأولياء الأمور في بداية العام الدراسي الجديد. نتمنى للجميع عاماً دراسياً موفقاً ومليئاً بالنجاح والتميز.', 'target' => 'all', 'is_pinned' => true],
            ['title' => 'موعد الاختبارات الشهرية', 'content' => 'نود إعلامكم بأن الاختبارات الشهرية ستبدأ يوم الأحد القادم. يرجى التأكد من استعداد أبنائكم.', 'target' => 'all', 'is_pinned' => false],
            ['title' => 'اجتماع أولياء الأمور', 'content' => 'ندعو جميع أولياء الأمور لحضور الاجتماع الدوري يوم الثلاثاء القادم الساعة 4 مساءً في قاعة المدرسة.', 'target' => 'parents', 'is_pinned' => true],
            ['title' => 'دورة تدريبية للمعلمين', 'content' => 'سيتم عقد دورة تدريبية حول أساليب التدريس الحديثة يوم الخميس القادم. الحضور إلزامي لجميع المعلمين.', 'target' => 'teachers', 'is_pinned' => false],
            ['title' => 'النشاط الرياضي الأسبوعي', 'content' => 'تذكير بموعد النشاط الرياضي الأسبوعي كل يوم أربعاء في الملعب الرياضي.', 'target' => 'students', 'is_pinned' => false],
            ['title' => 'إجازة اليوم الوطني', 'content' => 'تتمنى إدارة المدرسة لجميع منسوبيها إجازة سعيدة بمناسبة اليوم الوطني. سيتم استئناف الدراسة يوم الأحد.', 'target' => 'all', 'is_pinned' => false],
        ];

        foreach ($announcements as $data) {
            Announcement::firstOrCreate(
                ['title' => $data['title'], 'author_id' => $adminUser->id],
                [
                    'content' => $data['content'],
                    'target' => $data['target'],
                    'is_pinned' => $data['is_pinned'],
                ]
            );
        }

        echo "✅ الإعلانات: " . Announcement::count() . "\n";

        // ==========================================
        // 3. المستخدمون المعلّقون (طلبات الانضمام)
        // ==========================================
        $teacherRole = Role::where('slug', 'teacher')->first();

        $pendingUsers = [
            ['name' => 'ناصر محمد الشهراني', 'email' => 'nasser.pending@school.com', 'phone' => '0591111111', 'role_id' => $teacherRole ? $teacherRole->id : null],
            ['name' => 'هناء أحمد العمري', 'email' => 'hanaa.pending@school.com', 'phone' => '0592222222', 'role_id' => $teacherRole ? $teacherRole->id : null],
            ['name' => 'سلمان عبدالله الحربي', 'email' => 'salman.pending@school.com', 'phone' => '0593333333', 'role_id' => $parentRole ? $parentRole->id : null],
            ['name' => 'مريم خالد الزهراني', 'email' => 'mariam.pending@school.com', 'phone' => '0594444444', 'role_id' => $parentRole ? $parentRole->id : null],
            ['name' => 'عبدالكريم سعد المطيري', 'email' => 'abdulkarim.pending@school.com', 'phone' => '0595555555', 'role_id' => $parentRole ? $parentRole->id : null],
        ];

        foreach ($pendingUsers as $data) {
            if (!User::where('email', $data['email'])->exists()) {
User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make('password'),
                    'phone' => $data['phone'],
                    'role_id' => $data['role_id'],
                    'school_id' => $school ? $school->id : null,
                    'is_active' => false,
                ]);
            }
        }

        echo "✅ المستخدمون المعلّقون: " . User::where('is_active', false)->count() . "\n";

        echo "\n========================================\n";
        echo "🎉 تم إضافة الرسائل والإعلانات والمستخدمين المعلّقين بنجاح!\n";
        echo "========================================\n";
    }
}
