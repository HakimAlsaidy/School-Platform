<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إضافة school_id للجداول المتبقية
     */
    public function up(): void
    {
        // جدول الحضور
        if (Schema::hasTable('attendances') && !Schema::hasColumn('attendances', 'school_id')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->foreignId('school_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                $table->index('school_id');
            });
        }

        // جدول السلوك
        if (Schema::hasTable('behaviors') && !Schema::hasColumn('behaviors', 'school_id')) {
            Schema::table('behaviors', function (Blueprint $table) {
                $table->foreignId('school_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                $table->index('school_id');
            });
        }

        // جدول الجدول الدراسي
        if (Schema::hasTable('schedules') && !Schema::hasColumn('schedules', 'school_id')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->foreignId('school_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                $table->index('school_id');
            });
        }

        // جدول الدرجات
        if (Schema::hasTable('scores') && !Schema::hasColumn('scores', 'school_id')) {
            Schema::table('scores', function (Blueprint $table) {
                $table->foreignId('school_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                $table->index('school_id');
            });
        }

        // جدول الواجبات
        if (Schema::hasTable('assignments') && !Schema::hasColumn('assignments', 'school_id')) {
            Schema::table('assignments', function (Blueprint $table) {
                $table->foreignId('school_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                $table->index('school_id');
            });
        }

        // جدول تسليمات الواجبات
        if (Schema::hasTable('assignment_submissions') && !Schema::hasColumn('assignment_submissions', 'school_id')) {
            Schema::table('assignment_submissions', function (Blueprint $table) {
                $table->foreignId('school_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                $table->index('school_id');
            });
        }

        // جدول الرسائل
        if (Schema::hasTable('messages') && !Schema::hasColumn('messages', 'school_id')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->foreignId('school_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                $table->index('school_id');
            });
        }

        // جدول الإشعارات
        if (Schema::hasTable('notifications') && !Schema::hasColumn('notifications', 'school_id')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->foreignId('school_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                $table->index('school_id');
            });
        }

        // جدول الأحداث
        if (Schema::hasTable('events') && !Schema::hasColumn('events', 'school_id')) {
            Schema::table('events', function (Blueprint $table) {
                $table->foreignId('school_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                $table->index('school_id');
            });
        }

        // جدول سجل النشاط
        if (Schema::hasTable('activity_logs') && !Schema::hasColumn('activity_logs', 'school_id')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->foreignId('school_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                $table->index('school_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'attendances', 'behaviors', 'schedules', 'scores',
            'assignments', 'assignment_submissions', 'messages',
            'notifications', 'events', 'activity_logs'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'school_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropForeign(['school_id']);
                    $t->dropColumn('school_id');
                });
            }
        }
    }
};
