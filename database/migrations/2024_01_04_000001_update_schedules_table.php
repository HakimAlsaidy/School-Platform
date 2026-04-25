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
        Schema::table('schedules', function (Blueprint $table) {
            // إضافة رقم الحصة
            if (!Schema::hasColumn('schedules', 'period_number')) {
                $table->unsignedTinyInteger('period_number')->default(1)->after('day');
            }
        });

        // تحديث ENUM للأيام لإضافة السبت
        DB::statement("ALTER TABLE schedules MODIFY COLUMN day ENUM('saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            if (Schema::hasColumn('schedules', 'period_number')) {
                $table->dropColumn('period_number');
            }
        });

        DB::statement("ALTER TABLE schedules MODIFY COLUMN day ENUM('sunday', 'monday', 'tuesday', 'wednesday', 'thursday') NOT NULL");
    }
};
