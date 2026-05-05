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
        Schema::create('business_settings', function (Blueprint $table) {
            $table->id();
            $table->string('business_name_kh')->default('កាហ្វេ គរុកោសល្យ និងមីនីម៉ាត');
            $table->string('business_name_en')->default('BTEC Cafe & Mini Mart');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->string('tax_number')->nullable();
            $table->text('receipt_footer_text')->nullable();
            $table->string('currency_code')->default('KHR');
            $table->decimal('exchange_rate', 10, 2)->default(4100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_settings');
    }
};
