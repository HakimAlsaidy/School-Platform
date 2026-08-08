<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * يقوم بتشغيل جميع السيدرات المخصصة.
     */
    public function run(): void
    {
        $this->call([
            AccountsSeeder::class,
            FeatureDataSeeder::class,
            ExtraDataSeeder::class,
        ]);
    }
}
