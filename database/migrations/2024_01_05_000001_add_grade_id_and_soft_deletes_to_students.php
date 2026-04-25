<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // إضافة عمود الصف
            $table->foreignId('grade_id')->nullable()->after('classroom_id')->constrained()->onDelete('set null');
            
            // إضافة Soft Deletes
            $table->softDeletes();
        });

        // تحديث الطلاب الحاليين - ربطهم بالصفوف من خلال الفصول
        DB::statement('
            UPDATE students s
            INNER JOIN classrooms c ON s.classroom_id = c.id
            SET s.grade_id = c.grade_id
            WHERE s.classroom_id IS NOT NULL
        ');

        // جعل classroom_id قابل للـ null وتغيير onDelete
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('classroom_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['grade_id']);
            $table->dropColumn('grade_id');
            $table->dropSoftDeletes();
        });
    }
};
