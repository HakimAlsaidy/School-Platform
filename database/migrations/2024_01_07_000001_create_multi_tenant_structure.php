<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * تحويل المنصة إلى نظام متعدد المدارس (Multi-Tenant)
     */
    public function up(): void
    {
        // 1. إنشاء جدول المدارس
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');                                    // اسم المدرسة
            $table->string('name_en')->nullable();                     // الاسم بالإنجليزية
            $table->string('subdomain')->unique();                     // school1.platform.com
            $table->string('logo')->nullable();                        // شعار المدرسة
            $table->string('email')->nullable();                       // البريد الإلكتروني
            $table->string('phone', 20)->nullable();                   // رقم الهاتف
            $table->string('address')->nullable();                     // العنوان
            $table->string('city')->nullable();                        // المدينة
            $table->string('country')->default('السعودية');            // الدولة
            $table->string('principal_name')->nullable();              // اسم المدير
            $table->string('principal_phone', 20)->nullable();         // هاتف المدير
            $table->enum('type', ['public', 'private', 'international'])->default('private'); // نوع المدرسة
            $table->enum('level', ['elementary', 'middle', 'high', 'all'])->default('all');    // المرحلة
            $table->integer('max_students')->default(500);             // الحد الأقصى للطلاب
            $table->integer('max_teachers')->default(50);              // الحد الأقصى للمعلمين
            $table->date('license_expires_at')->nullable();            // تاريخ انتهاء الترخيص
            $table->boolean('is_active')->default(false);              // حالة التفعيل
            $table->boolean('is_verified')->default(false);            // تم التحقق
            $table->json('settings')->nullable();                      // إعدادات المدرسة
            $table->text('notes')->nullable();                         // ملاحظات
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. إنشاء جدول اشتراكات المدارس
        Schema::create('school_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->enum('plan', ['free', 'basic', 'premium', 'enterprise'])->default('free');
            $table->decimal('price', 10, 2)->default(0);
            $table->date('starts_at');
            $table->date('ends_at');
            $table->boolean('is_active')->default(true);
            $table->json('features')->nullable();                      // المميزات المتاحة
            $table->timestamps();
        });

        // 3. إضافة school_id للجداول الموجودة
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->boolean('is_super_admin')->default(false)->after('is_active');
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->boolean('is_global')->default(false)->after('color'); // مواد عامة للجميع
        });

        Schema::table('classrooms', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });

        Schema::table('guardians', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });

        // إضافة Index للأداء
        Schema::table('users', function (Blueprint $table) {
            $table->index(['school_id', 'is_active']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->index(['school_id', 'classroom_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إزالة الأعمدة
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });

        Schema::table('guardians', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });

        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn(['school_id', 'is_global']);
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn(['school_id', 'is_super_admin']);
        });

        Schema::dropIfExists('school_subscriptions');
        Schema::dropIfExists('schools');
    }
};
