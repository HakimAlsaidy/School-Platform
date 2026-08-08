<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // جدول الرسوم الدراسية
        Schema::create('fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['tuition', 'books', 'transport', 'activities', 'other'])->default('tuition');
            $table->enum('frequency', ['one_time', 'term', 'semester', 'yearly'])->default('one_time');
            $table->enum('status', ['draft', 'active', 'inactive'])->default('draft');
            $table->date('due_date')->nullable();
            $table->boolean('is_installment')->default(false);
            $table->integer('installments_count')->nullable();
            $table->timestamps();
        });

        // جدول فرض الرسوم على الطلاب
        Schema::create('student_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('fee_id')->constrained('fees')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('status', ['pending', 'partial', 'paid', 'overdue'])->default('pending');
            $table->decimal('amount_due', 10, 2);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->date('due_date');
            $table->timestamps();
            $table->unique(['fee_id', 'student_id']);
        });

        // جدول المدفوعات
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('student_fee_id')->nullable()->constrained('student_fees')->onDelete('set null');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('payment_ref')->unique();
            $table->decimal('amount', 10, 2);
            $table->enum('method', ['cash', 'card', 'transfer', 'online', 'wallet'])->default('cash');
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('completed');
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // جدول المصاريف
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('category', ['salaries', 'utilities', 'supplies', 'maintenance', 'transport', 'activities', 'other'])->default('other');
            $table->date('expense_date');
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->string('receipt_path')->nullable();
            $table->timestamps();
        });

        // جدول الإيرادات الأخرى
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('category', ['tuition', 'donation', 'funding', 'rental', 'other'])->default('tuition');
            $table->date('income_date');
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incomes');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('student_fees');
        Schema::dropIfExists('fees');
    }
};

