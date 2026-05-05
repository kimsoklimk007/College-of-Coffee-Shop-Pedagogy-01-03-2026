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
        Schema::create('employee_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            
            // Document Details
            $table->string('document_type'); // Contract, ID Card, Passport, Medical Certificate, etc.
            $table->string('document_type_kh')->nullable();
            $table->string('document_name');
            $table->string('document_path'); // File path
            $table->string('original_filename'); // Original uploaded filename
            $table->string('mime_type'); // File MIME type
            $table->integer('file_size'); // File size in bytes
            
            // Document Status
            $table->enum('status', ['Active', 'Expired', 'Replaced'])->default('Active');
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('requires_renewal')->default(false);
            
            // Verification
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            
            // System Fields
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('employee_id');
            $table->index('document_type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_files');
    }
};
