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
        Schema::create('employee_activity_logs', function (Blueprint $table) {
            $table->id();
            
            // User Information
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('employee_id')->nullable()->constrained()->onDelete('set null');
            $table->string('user_name')->nullable(); // Denormalized for performance
            $table->string('user_role')->nullable();
            
            // Activity Details
            $table->string('action'); // create, update, delete, login, logout, etc.
            $table->string('module'); // employee, attendance, payroll, etc.
            $table->string('description');
            $table->text('description_kh')->nullable();
            
            // Target Information
            $table->string('target_type')->nullable(); // Model class name
            $table->string('target_id')->nullable(); // Model ID
            $table->string('target_name')->nullable(); // Human-readable target name
            
            // Data Changes
            $table->json('old_values')->nullable(); // Previous values before change
            $table->json('new_values')->nullable(); // New values after change
            
            // Request Information
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('device_info')->nullable();
            $table->string('location')->nullable(); // GPS if available
            
            // System Fields
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('employee_id');
            $table->index('action');
            $table->index('module');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_activity_logs');
    }
};
