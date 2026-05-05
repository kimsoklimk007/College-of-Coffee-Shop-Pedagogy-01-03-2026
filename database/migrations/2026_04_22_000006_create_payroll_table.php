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
        Schema::create('employee_payroll', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            
            // Payroll Period
            $table->string('payroll_period'); // e.g., "2026-04", "April-2026"
            $table->date('period_start');
            $table->date('period_end');
            $table->date('payment_date')->nullable();
            
            // Base Salary
            $table->decimal('base_salary', 10, 2);
            $table->decimal('daily_rate', 8, 2);
            $table->decimal('hourly_rate', 8, 2);
            
            // Attendance Summary
            $table->integer('working_days')->default(0); // Total working days in period
            $table->integer('present_days')->default(0);
            $table->integer('late_days')->default(0);
            $table->integer('absent_days')->default(0);
            $table->integer('leave_days')->default(0);
            $table->integer('holidays')->default(0);
            
            // Earnings
            $table->decimal('basic_earnings', 10, 2)->default(0);
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->decimal('overtime_rate', 5, 2)->default(1.5);
            $table->decimal('overtime_earnings', 10, 2)->default(0);
            $table->decimal('holiday_earnings', 10, 2)->default(0);
            $table->decimal('bonus', 10, 2)->default(0);
            $table->decimal('allowances', 10, 2)->default(0);
            $table->decimal('total_earnings', 10, 2)->default(0);
            
            // Deductions
            $table->decimal('absent_deductions', 10, 2)->default(0);
            $table->decimal('late_deductions', 10, 2)->default(0);
            $table->decimal('tax_deductions', 10, 2)->default(0);
            $table->decimal('social_security', 10, 2)->default(0);
            $table->decimal('other_deductions', 10, 2)->default(0);
            $table->decimal('total_deductions', 10, 2)->default(0);
            
            // Net Salary
            $table->decimal('net_salary', 10, 2)->default(0);
            $table->string('currency')->default('USD');
            
            // Status and Approval
            $table->enum('status', ['Draft', 'Pending', 'Approved', 'Paid', 'Cancelled'])->default('Draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('approved_at')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('paid_at')->nullable();
            
            // Payment Method
            $table->enum('payment_method', ['Bank Transfer', 'Cash', 'Check'])->default('Bank Transfer');
            $table->string('transaction_reference')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Unique constraint to prevent duplicate payroll for same period
            $table->unique(['employee_id', 'payroll_period']);
            $table->index('payroll_period');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_payroll');
    }
};
