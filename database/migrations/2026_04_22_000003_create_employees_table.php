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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->unique(); // Unique employee ID like EMP001
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Link to users table
            $table->foreignId('role_id')->nullable()->constrained('employee_roles')->onDelete('set null');
            $table->foreignId('shift_id')->nullable()->constrained()->onDelete('set null');
            
            // Personal Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('first_name_kh')->nullable();
            $table->string('last_name_kh')->nullable();
            $table->string('gender');
            $table->date('date_of_birth');
            $table->string('nationality')->default('Cambodian');
            $table->string('id_card_number')->unique()->nullable(); // National ID
            $table->string('passport_number')->unique()->nullable();
            
            // Contact Information
            $table->string('phone');
            $table->string('email')->unique();
            $table->text('address');
            $table->text('address_kh')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            
            // Employment Information
            $table->date('hire_date');
            $table->date('end_date')->nullable(); // For resigned employees
            $table->decimal('base_salary', 10, 2);
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_name')->nullable();
            
            // Status and Settings
            $table->enum('status', ['Active', 'Inactive', 'On Leave', 'Resigned', 'Terminated'])->default('Active');
            $table->enum('employment_type', ['Full-time', 'Part-time', 'Contract', 'Intern'])->default('Full-time');
            $table->boolean('has_qr_code')->default(false);
            $table->string('qr_code_path')->nullable();
            
            // Documents
            $table->string('profile_photo')->nullable();
            $table->string('id_card_photo')->nullable();
            $table->string('contract_document')->nullable();
            $table->text('other_documents')->nullable(); // JSON array of document paths
            
            // System Fields
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
            
            $table->index('employee_id');
            $table->index('status');
            $table->index('role_id');
            $table->index('shift_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
