<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->decimal('stock_quantity', 10, 2)->default(0)->after('cost_price');
            $table->decimal('low_stock_threshold', 10, 2)->default(10)->after('stock_quantity');
            $table->unsignedBigInteger('category_id')->nullable()->after('low_stock_threshold');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['stock_quantity', 'low_stock_threshold', 'category_id']);
        });
    }
};
