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
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('shift_id')->nullable()->constrained()->onDelete('set null');
            
            // Attendance Date and Time
            $table->date('attendance_date');
            $table->datetime('check_in_time')->nullable();
            $table->datetime('check_out_time')->nullable();
            $table->datetime('break_start_time')->nullable();
            $table->datetime('break_end_time')->nullable();
            
            // Attendance Status
            $table->enum('status', ['Present', 'Late', 'Absent', 'Half Day', 'On Leave'])->default('Present');
            $table->enum('approval_status', ['Pending', 'Approved', 'Rejected'])->default('Approved');
            
            // Time Calculations
            $table->integer('total_work_minutes')->default(0); // Total work time in minutes
            $table->integer('overtime_minutes')->default(0); // Overtime in minutes
            $table->integer('late_minutes')->default(0); // Late arrival in minutes
            $table->integer('early_departure_minutes')->default(0); // Early departure in minutes
            
            // Location and Anti-Cheat
            $table->string('check_in_location')->nullable(); // GPS coordinates
            $table->string('check_out_location')->nullable();
            $table->string('device_info')->nullable(); // Device fingerprint
            $table->string('ip_address')->nullable();
            $table->string('check_in_photo')->nullable(); // Photo for verification
            $table->string('check_out_photo')->nullable();
            
            // Notes and Approval
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Unique constraint to prevent duplicate attendance records
            $table->unique(['employee_id', 'attendance_date']);
            $table->index('attendance_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
