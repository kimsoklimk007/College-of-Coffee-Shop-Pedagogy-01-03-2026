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
        Schema::create('employee_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            
            // Leave Details
            $table->enum('leave_type', ['Annual', 'Sick', 'Personal', 'Maternity', 'Paternity', 'Unpaid', 'Emergency']);
            $table->text('reason');
            $table->text('reason_kh')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days'); // Calculated number of leave days
            
            // Leave Status
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Cancelled'])->default('Pending');
            $table->text('rejection_reason')->nullable();
            $table->text('rejection_reason_kh')->nullable();
            
            // Approval Workflow
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('approved_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('rejected_at')->nullable();
            
            // Attachments
            $table->string('attachment')->nullable(); // Medical certificate, etc.
            $table->text('notes')->nullable();
            
            // Leave Balance Impact
            $table->boolean('paid_leave')->default(true);
            $table->decimal('deducted_from_balance', 5, 2)->default(0); // Days deducted from annual leave
            
            $table->timestamps();
            
            $table->index('employee_id');
            $table->index('status');
            $table->index('leave_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_leaves');
    }
};
