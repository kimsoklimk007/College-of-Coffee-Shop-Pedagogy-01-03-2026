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
        Schema::create('employee_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Admin, Manager, Staff, Cashier, etc.
            $table->string('name_kh')->nullable(); // Khmer translation
            $table->text('description')->nullable();
            $table->text('description_kh')->nullable();
            $table->json('permissions')->nullable(); // JSON array of permissions
            $table->decimal('base_salary', 10, 2)->nullable(); // Base salary for this role
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_roles');
    }
};
