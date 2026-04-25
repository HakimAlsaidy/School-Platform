<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إضافة أعمدة الترم والشهر للدرجات
     */
    public function up(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            // الترم: 1 أو 2
            $table->tinyInteger('term')->default(1)->after('semester'); // الترم الأول أو الثاني
            
            // الشهر: 1, 2, 3 أو null للمحصلة والنهائي
            $table->tinyInteger('month')->nullable()->after('term'); // الشهر الأول/الثاني/الثالث أو null
            
            // مجموع الشهر (الحضور + الواجبات + المواظبة + التحريري)
            $table->decimal('month_total', 5, 2)->nullable()->after('written'); // مجموع الشهر من 100
            
            // تحديث فهرس للبحث السريع
            $table->index(['student_id', 'subject_id', 'term', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'subject_id', 'term', 'month']);
            $table->dropColumn(['term', 'month', 'month_total']);
        });
    }
};
