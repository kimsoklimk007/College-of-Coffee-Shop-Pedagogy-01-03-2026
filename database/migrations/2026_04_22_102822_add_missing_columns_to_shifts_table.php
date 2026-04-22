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
        Schema::table('shifts', function (Blueprint $table) {
            $table->string('name_kh')->nullable()->after('name');
            $table->integer('duration_minutes')->after('end_time');
            $table->decimal('overtime_rate', 5, 2)->default(1.5)->after('duration_minutes');
            $table->boolean('is_active')->default(true)->after('overtime_rate');
            $table->integer('late_threshold')->default(15)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn(['name_kh', 'duration_minutes', 'overtime_rate', 'is_active', 'late_threshold']);
        });
    }
};
