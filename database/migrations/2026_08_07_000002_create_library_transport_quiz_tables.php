<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ==================== نظام المكتبة ====================
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('title');
            $table->string('author')->nullable();
            $table->string('isbn')->nullable();
            $table->string('publisher')->nullable();
            $table->string('category')->nullable();
            $table->integer('total_copies')->default(1);
            $table->integer('available_copies')->default(1);
            $table->string('shelf_location')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('book_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->date('loan_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->enum('status', ['borrowed', 'returned', 'overdue', 'lost'])->default('borrowed');
            $table->text('notes')->nullable();
            $table->foreignId('issued_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // ==================== نظام النقل المدرسي ====================
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('bus_number')->unique();
            $table->string('plate_number')->nullable();
            $table->integer('capacity')->default(40);
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->string('supervisor_name')->nullable();
            $table->string('supervisor_phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('transport_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('bus_id')->constrained('buses')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->time('pickup_time')->nullable();
            $table->time('dropoff_time')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('transport_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('route_id')->constrained('transport_routes')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('pickup_point')->nullable();
            $table->string('dropoff_point')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['route_id', 'student_id']);
        });

        // ==================== نظام الاختبارات الإلكترونية ====================
        Schema::create('question_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('grade_id')->nullable()->constrained('grades')->onDelete('set null');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->enum('type', ['multiple_choice', 'true_false', 'short_answer', 'essay']);
            $table->text('question');
            $table->json('options')->nullable();
            $table->text('correct_answer');
            $table->integer('points')->default(1);
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->timestamps();
        });

        Schema::create('online_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('classroom_id')->constrained('classrooms')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('duration_minutes')->default(30);
            $table->integer('total_points')->default(100);
            $table->boolean('is_published')->default(false);
            $table->datetime('start_at')->nullable();
            $table->datetime('end_at')->nullable();
            $table->boolean('allow_retake')->default(false);
            $table->timestamps();
        });

        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('online_quiz_id')->constrained('online_quizzes')->onDelete('cascade');
            $table->foreignId('question_bank_id')->nullable()->constrained('question_banks')->onDelete('set null');
            $table->text('question');
            $table->enum('type', ['multiple_choice', 'true_false', 'short_answer', 'essay']);
            $table->json('options')->nullable();
            $table->text('correct_answer');
            $table->integer('points')->default(1);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('online_quiz_id')->constrained('online_quizzes')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->integer('score')->default(0);
            $table->integer('max_score')->default(0);
            $table->json('answers')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->enum('status', ['in_progress', 'submitted'])->default('in_progress');
            $table->timestamps();
        });

        // ==================== المواد الدراسية والملفات ====================
        Schema::create('classroom_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('classroom_id')->constrained('classrooms')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['file', 'link', 'text', 'video'])->default('file');
            $table->string('file_path')->nullable();
            $table->string('external_url')->nullable();
            $table->text('content')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classroom_materials');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('online_quizzes');
        Schema::dropIfExists('question_banks');
        Schema::dropIfExists('transport_students');
        Schema::dropIfExists('transport_routes');
        Schema::dropIfExists('buses');
        Schema::dropIfExists('book_loans');
        Schema::dropIfExists('books');
    }
};
