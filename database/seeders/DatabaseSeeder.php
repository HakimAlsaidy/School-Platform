<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * يقوم بتشغيل جميع الـ Seeders في المشروع بالترتيب الصحيح:
     * 1. AccountsSeeder: الأدوار والمدرسة والصفوف والمواد والفصول والحسابات
     * 2. ExtraDataSeeder: الرسائل والإعلانات والمستخدمين المعلّقين
     * 3. FeatureDataSeeder: بيانات الوحدات الوظيفية (المالية، المكتبة، النقل، الاختبارات، المواد)
     */
    public function run(): void
    {
        $this->call([
            AccountsSeeder::class,
            ExtraDataSeeder::class,
            FeatureDataSeeder::class,
        ]);
    }
}

