<?php

/**
 * ملف لحذف جميع البيانات من قاعدة البيانات ما عدا Super Admin
 * 
 * لتشغيله: php clear_database.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🗑️ بدء حذف البيانات...\n\n";

try {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    $tables = [
        'scores',
        'attendances', 
        'behaviors',
        'assignment_submissions',
        'assignments',
        'schedules',
        'students',
        'teacher_classroom',
        'teacher_subject',
        'grade_subject',
        'teachers',
        'guardians',
        'classrooms',
        'subjects',
        'grades',
        'announcements',
        'messages',
        'notifications',
        'activity_logs',
        'events',
        'school_subscriptions',
        'schools',
    ];

    foreach ($tables as $table) {
        if (Schema::hasTable($table)) {
            DB::table($table)->truncate();
            echo "✅ تم حذف جدول: {$table}\n";
        }
    }

    // حذف المستخدمين ما عدا Super Admin
    $deleted = DB::table('users')->where(function($q) {
        $q->whereNull('is_super_admin')
          ->orWhere('is_super_admin', false);
    })->delete();
    
    echo "\n✅ تم حذف {$deleted} مستخدم (ما عدا Super Admin)\n";

    // حذف الأدوار ما عدا الأدوار الأساسية
    // DB::table('roles')->truncate(); // إذا أردت حذف الأدوار أيضاً

    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    echo "\n╔════════════════════════════════════════╗\n";
    echo "║  ✅ تم حذف جميع البيانات بنجاح!       ║\n";
    echo "║  🔐 حساب Super Admin محفوظ            ║\n";
    echo "╚════════════════════════════════════════╝\n";

} catch (\Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
}
