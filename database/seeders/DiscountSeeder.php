<?php

namespace Database\Seeders;

use App\Models\Discount;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get products to apply discounts to
        $brownieCake = Product::where('name', 'Brownie Cake')->first();
        $espresso = Product::where('name', 'Espresso')->first();

        $discounts = [
            [
                'product_id' => $brownieCake ? $brownieCake->id : 1,
                'discount_percentage' => 2.00,
                'start_date' => now()->subDays(10)->toDateString(),
                'end_date' => now()->addDays(20)->toDateString(),
            ],
            [
                'product_id' => $espresso ? $espresso->id : 2,
                'discount_percentage' => 5.00,
                'start_date' => now()->subDays(5)->toDateString(),
                'end_date' => now()->addDays(25)->toDateString(),
            ],
        ];

        foreach ($discounts as $discountData) {
            Discount::firstOrCreate([
                'product_id' => $discountData['product_id'],
                'discount_percentage' => $discountData['discount_percentage'],
                'start_date' => $discountData['start_date'],
                'end_date' => $discountData['end_date'],
            ]);
        }
    }
}
