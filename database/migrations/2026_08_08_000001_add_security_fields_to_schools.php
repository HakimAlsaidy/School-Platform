<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إضافة حقول الأمان والتوثيق والمصادقة لجدول المدارس
     */
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            // حقول التوثيق والمصادقة
            if (!Schema::hasColumn('schools', 'verification_code')) {
                $table->string('verification_code', 6)->nullable()->after('notes');
            }
            if (!Schema::hasColumn('schools', 'verification_expires_at')) {
                $table->timestamp('verification_expires_at')->nullable()->after('verification_code');
            }
            if (!Schema::hasColumn('schools', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verification_expires_at');
            }

            // حماية من البوتات
            if (!Schema::hasColumn('schools', 'registration_ip')) {
                $table->string('registration_ip', 45)->nullable()->after('verified_at');
            }
            if (!Schema::hasColumn('schools', 'registration_user_agent')) {
                $table->string('registration_user_agent', 500)->nullable()->after('registration_ip');
            }
            if (!Schema::hasColumn('schools', 'registration_completed_at')) {
                $table->timestamp('registration_completed_at')->nullable()->after('registration_user_agent');
            }

            // تتبع الطلبات
            if (!Schema::hasColumn('schools', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('registration_completed_at');
            }
            if (!Schema::hasColumn('schools', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('rejection_reason');
            }
            if (!Schema::hasColumn('schools', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
        });

        // إضافة فهارس للأداء
        Schema::table('schools', function (Blueprint $table) {
            $table->index(['is_active', 'is_verified']);
            $table->index(['registration_ip', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'verification_code',
                'verification_expires_at',
                'verified_at',
                'registration_ip',
                'registration_user_agent',
                'registration_completed_at',
                'rejection_reason',
                'approved_by',
                'approved_at',
            ]);

            $table->dropIndex(['is_active', 'is_verified']);
            $table->dropIndex(['registration_ip', 'created_at']);
        });
    }
};
