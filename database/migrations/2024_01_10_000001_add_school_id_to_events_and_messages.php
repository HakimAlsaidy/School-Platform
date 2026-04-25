<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // إضافة school_id للأحداث
        if (Schema::hasTable('events') && !Schema::hasColumn('events', 'school_id')) {
            Schema::table('events', function (Blueprint $table) {
                $table->foreignId('school_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            });
        }

        // إضافة school_id للرسائل (اختياري للتصفية)
        if (Schema::hasTable('messages') && !Schema::hasColumn('messages', 'school_id')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->foreignId('school_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('events', 'school_id')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropForeign(['school_id']);
                $table->dropColumn('school_id');
            });
        }

        if (Schema::hasColumn('messages', 'school_id')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->dropForeign(['school_id']);
                $table->dropColumn('school_id');
            });
        }
    }
};
