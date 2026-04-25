<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * تحديث جدول activity_logs لدعم Audit Log المحسّن
     */
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            // إضافة عمود school_id للفصل
            if (!Schema::hasColumn('activity_logs', 'school_id')) {
                $table->foreignId('school_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            }

            // إضافة القيم القديمة والجديدة
            if (!Schema::hasColumn('activity_logs', 'old_values')) {
                $table->json('old_values')->nullable()->after('description');
            }

            if (!Schema::hasColumn('activity_logs', 'new_values')) {
                $table->json('new_values')->nullable()->after('old_values');
            }

            // إضافة User Agent
            if (!Schema::hasColumn('activity_logs', 'user_agent')) {
                $table->string('user_agent', 500)->nullable()->after('ip_address');
            }

            // إضافة فهارس للبحث السريع
            $table->index(['school_id', 'created_at']);
            $table->index(['user_id', 'action']);
            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['school_id', 'created_at']);
            $table->dropIndex(['user_id', 'action']);
            $table->dropIndex(['model_type', 'model_id']);

            $table->dropColumn(['school_id', 'old_values', 'new_values', 'user_agent']);
        });
    }
};
