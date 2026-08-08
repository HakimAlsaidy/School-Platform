<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * متوافق مع MySQL و PostgreSQL.
     */
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            // إضافة رقم الحصة
            if (!Schema::hasColumn('schedules', 'period_number')) {
                $table->unsignedTinyInteger('period_number')->default(1)->after('day');
            }
        });

        // تحديث قيم الأيام لإضافة السبت
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            // MySQL ليس هو القاعدة هنا، نضيف قيد تحقق جديد بدون إسقاط القديم
            // نستخدم string بدلاً من enum لأن PostgreSQL لا يدعم ALTER ENUM بسهولة
            DB::statement("ALTER TABLE schedules ALTER COLUMN day TYPE VARCHAR(20)");
        } elseif ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("ALTER TABLE schedules MODIFY COLUMN day ENUM('saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday') NOT NULL");
        }
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

        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE schedules ALTER COLUMN day TYPE VARCHAR(20)");
        } elseif ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("ALTER TABLE schedules MODIFY COLUMN day ENUM('sunday', 'monday', 'tuesday', 'wednesday', 'thursday') NOT NULL");
        }
    }
};
