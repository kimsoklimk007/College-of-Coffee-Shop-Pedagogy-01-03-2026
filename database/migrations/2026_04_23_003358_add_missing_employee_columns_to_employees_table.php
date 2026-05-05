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
        Schema::table('employees', function (Blueprint $table) {
            // Foreign key columns
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('set null');
            
            // Personal Information columns
            $table->string('first_name_kh')->nullable()->after('last_name');
            $table->string('last_name_kh')->nullable()->after('first_name_kh');
            $table->string('gender')->after('last_name_kh');
            $table->string('nationality')->default('Cambodian')->after('date_of_birth');
            $table->string('id_card_number')->unique()->nullable()->after('nationality');
            $table->string('passport_number')->unique()->nullable()->after('id_card_number');
            
            // Contact Information columns
            $table->text('address_kh')->nullable()->after('address');
            $table->string('city')->nullable()->after('address_kh');
            $table->string('province')->nullable()->after('city');
            $table->string('emergency_contact_name')->nullable()->after('province');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            
            // Employment Information columns
            $table->date('end_date')->nullable()->after('hire_date');
            $table->string('bank_name')->nullable()->after('base_salary');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_account_name')->nullable()->after('bank_account_number');
            
            // Status and Settings columns
            $table->enum('employment_type', ['Full-time', 'Part-time', 'Contract', 'Intern'])->default('Full-time')->after('status');
            $table->boolean('has_qr_code')->default(false)->after('employment_type');
            $table->string('id_card_photo')->nullable()->after('profile_photo');
            $table->string('contract_document')->nullable()->after('id_card_photo');
            $table->text('other_documents')->nullable()->after('contract_document');
            
            // System Fields
            $table->text('notes')->nullable()->after('other_documents');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn([
                'user_id',
                'first_name_kh',
                'last_name_kh',
                'gender',
                'nationality',
                'id_card_number',
                'passport_number',
                'address_kh',
                'city',
                'province',
                'emergency_contact_name',
                'emergency_contact_phone',
                'end_date',
                'bank_name',
                'bank_account_number',
                'bank_account_name',
                'employment_type',
                'has_qr_code',
                'id_card_photo',
                'contract_document',
                'other_documents',
                'notes',
                'created_by',
                'updated_by'
            ]);
        });
    }
};
