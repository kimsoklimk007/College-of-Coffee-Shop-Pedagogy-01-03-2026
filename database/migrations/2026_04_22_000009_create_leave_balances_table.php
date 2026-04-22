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
        Schema::create('employee_leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            
            // Leave Type and Balance
            $table->enum('leave_type', ['Annual', 'Sick', 'Personal', 'Maternity', 'Paternity']);
            $table->decimal('total_days', 5, 2)->default(0); // Total allocated days
            $table->decimal('used_days', 5, 2)->default(0); // Days used
            $table->decimal('remaining_days', 5, 2)->default(0); // Available days
            $table->decimal('pending_days', 5, 2)->default(0); // Days in pending requests
            
            // Period
            $table->integer('year'); // Calendar year
            $table->date('reset_date')->nullable(); // When balance resets
            
            $table->timestamps();
            
            // Unique constraint for one balance per employee per leave type per year
            $table->unique(['employee_id', 'leave_type', 'year']);
            $table->index('employee_id');
            $table->index('leave_type');
            $table->index('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_leave_balances');
    }
};
