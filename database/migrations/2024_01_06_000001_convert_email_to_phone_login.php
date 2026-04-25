<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * تحويل نظام تسجيل الدخول من البريد الإلكتروني إلى رقم الهاتف
     */
    public function up(): void
    {
        // 1. تحديث جدول المستخدمين
        Schema::table('users', function (Blueprint $table) {
            // جعل البريد الإلكتروني اختياري
            $table->string('email')->nullable()->change();
            
            // إضافة عمود الهاتف إذا لم يكن موجوداً
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('email');
            }
        });

        // 2. تحديث البيانات الموجودة - نقل البريد إلى رقم هاتف وهمي إذا لم يكن موجود
        DB::table('users')->whereNull('phone')->orWhere('phone', '')->update([
            'phone' => DB::raw("CONCAT('05', LPAD(id, 8, '0'))")
        ]);

        // 3. جعل رقم الهاتف فريد
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->unique()->change();
        });

        // 4. إزالة قيد unique من البريد الإلكتروني
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email']);
        });  

        // 5. تحديث جدول password_reset_tokens ليستخدم الهاتف
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropPrimary();
            $table->string('email')->nullable()->change();
            $table->string('phone', 20)->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['phone']);
            $table->string('email')->unique()->change();
        });

        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->string('email')->primary()->change();
        });
    }
};
