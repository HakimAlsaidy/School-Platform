<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إضافة أعمدة الدرجات التفصيلية
     */
    public function up(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            // درجات الأعمال الفصلية
            $table->decimal('attendance', 5, 2)->nullable()->after('max_score'); // الحضور من 20
            $table->decimal('homework', 5, 2)->nullable()->after('attendance'); // الواجبات من 20
            $table->decimal('discipline', 5, 2)->nullable()->after('homework'); // المواظبة من 20
            $table->decimal('written', 5, 2)->nullable()->after('discipline'); // التحريري من 40
            $table->decimal('total_20', 5, 2)->nullable()->after('written'); // المحصلة من 20
            $table->decimal('final_30', 5, 2)->nullable()->after('total_20'); // النهائي من 30
            $table->decimal('total_50', 5, 2)->nullable()->after('final_30'); // المجموع من 50
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->dropColumn([
                'attendance',
                'homework',
                'discipline',
                'written',
                'total_20',
                'final_30',
                'total_50'
            ]);
        });
    }
};
