<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategorySize;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Exchange rate for KHR to USD
        $exchangeRate = 4100;

        // Create categories with size templates and pricing
        $categories = [
            [
                'name' => 'Hot Coffee',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 12000, 'price_usd' => 2.93],
                    ['size' => 'M', 'price_khr' => 15000, 'price_usd' => 3.66],
                    ['size' => 'L', 'price_khr' => 18000, 'price_usd' => 4.39],
                    ['size' => 'XXL', 'price_khr' => 22000, 'price_usd' => 5.37],
                ],
            ],
            [
                'name' => 'Iced Coffee',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 14000, 'price_usd' => 3.41],
                    ['size' => 'M', 'price_khr' => 17000, 'price_usd' => 4.15],
                    ['size' => 'L', 'price_khr' => 20000, 'price_usd' => 4.88],
                    ['size' => 'XXL', 'price_khr' => 25000, 'price_usd' => 6.10],
                ],
            ],
            [
                'name' => 'Flavored Coffee',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 15000, 'price_usd' => 3.66],
                    ['size' => 'M', 'price_khr' => 18000, 'price_usd' => 4.39],
                    ['size' => 'L', 'price_khr' => 22000, 'price_usd' => 5.37],
                    ['size' => 'XXL', 'price_khr' => 28000, 'price_usd' => 6.83],
                ],
            ],
            [
                'name' => 'Non-Coffee Drinks',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 10000, 'price_usd' => 2.44],
                    ['size' => 'M', 'price_khr' => 13000, 'price_usd' => 3.17],
                    ['size' => 'L', 'price_khr' => 16000, 'price_usd' => 3.90],
                    ['size' => 'XXL', 'price_khr' => 20000, 'price_usd' => 4.88],
                ],
            ],
        ];

        $categoryModels = [];

        foreach ($categories as $categoryData) {
            $category = Category::firstOrCreate([
                'name' => $categoryData['name'],
            ], [
                'start_date' => now(),
            ]);

            $categoryModels[$categoryData['name']] = $category;

            // Create category size templates
            foreach ($categoryData['sizes'] as $sizeData) {
                CategorySize::firstOrCreate([
                    'category_id' => $category->id,
                    'size' => $sizeData['size'],
                ], [
                    'price_khr' => $sizeData['price_khr'],
                    'price_usd' => $sizeData['price_usd'],
                    'is_active' => true,
                ]);
            }
        }

        // Create products with sizes - Hot Coffee (1-10)
        $products = [
            // ☕ ប្រភេទកាហ្វេពេញនិយម (Hot Coffee)
            [
                'name' => 'Espresso',
                'name_kh' => 'អេស្ព្រេសូ',
                'category_name' => 'Hot Coffee',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 10000, 'price_usd' => 2.44],
                    ['size' => 'M', 'price_khr' => 12000, 'price_usd' => 2.93],
                    ['size' => 'L', 'price_khr' => 15000, 'price_usd' => 3.66],
                    ['size' => 'XXL', 'price_khr' => 18000, 'price_usd' => 4.39],
                ],
                'qty' => 200
            ],
            [
                'name' => 'Americano',
                'name_kh' => 'អាមេរិកាណូ',
                'category_name' => 'Hot Coffee',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 12000, 'price_usd' => 2.93],
                    ['size' => 'M', 'price_khr' => 15000, 'price_usd' => 3.66],
                    ['size' => 'L', 'price_khr' => 18000, 'price_usd' => 4.39],
                    ['size' => 'XXL', 'price_khr' => 22000, 'price_usd' => 5.37],
                ],
                'qty' => 180
            ],
            [
                'name' => 'Cappuccino',
                'name_kh' => 'កាពូឈីណូ',
                'category_name' => 'Hot Coffee',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 14000, 'price_usd' => 3.41],
                    ['size' => 'M', 'price_khr' => 17000, 'price_usd' => 4.15],
                    ['size' => 'L', 'price_khr' => 20000, 'price_usd' => 4.88],
                    ['size' => 'XXL', 'price_khr' => 25000, 'price_usd' => 6.10],
                ],
                'qty' => 150
            ],
            [
                'name' => 'Latte',
                'name_kh' => 'ឡាតេ',
                'category_name' => 'Hot Coffee',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 14000, 'price_usd' => 3.41],
                    ['size' => 'M', 'price_khr' => 17000, 'price_usd' => 4.15],
                    ['size' => 'L', 'price_khr' => 20000, 'price_usd' => 4.88],
                    ['size' => 'XXL', 'price_khr' => 25000, 'price_usd' => 6.10],
                ],
                'qty' => 150
            ],
            [
                'name' => 'Mocha',
                'name_kh' => 'ម៉ូកា',
                'category_name' => 'Hot Coffee',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 15000, 'price_usd' => 3.66],
                    ['size' => 'M', 'price_khr' => 18000, 'price_usd' => 4.39],
                    ['size' => 'L', 'price_khr' => 22000, 'price_usd' => 5.37],
                    ['size' => 'XXL', 'price_khr' => 28000, 'price_usd' => 6.83],
                ],
                'qty' => 120
            ],
            [
                'name' => 'Macchiato',
                'name_kh' => 'ម៉ាក្យាតូ',
                'category_name' => 'Hot Coffee',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 13000, 'price_usd' => 3.17],
                    ['size' => 'M', 'price_khr' => 16000, 'price_usd' => 3.90],
                    ['size' => 'L', 'price_khr' => 19000, 'price_usd' => 4.63],
                    ['size' => 'XXL', 'price_khr' => 23000, 'price_usd' => 5.61],
                ],
                'qty' => 130
            ],
            [
                'name' => 'Flat White',
                'name_kh' => 'ផ្លាតវ៉ាយ',
                'category_name' => 'Hot Coffee',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 14000, 'price_usd' => 3.41],
                    ['size' => 'M', 'price_khr' => 17000, 'price_usd' => 4.15],
                    ['size' => 'L', 'price_khr' => 20000, 'price_usd' => 4.88],
                    ['size' => 'XXL', 'price_khr' => 25000, 'price_usd' => 6.10],
                ],
                'qty' => 140
            ],
            [
                'name' => 'Affogato',
                'name_kh' => 'អាហ្វូហ្គាតូ',
                'category_name' => 'Hot Coffee',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 16000, 'price_usd' => 3.90],
                    ['size' => 'M', 'price_khr' => 20000, 'price_usd' => 4.88],
                    ['size' => 'L', 'price_khr' => 25000, 'price_usd' => 6.10],
                    ['size' => 'XXL', 'price_khr' => 30000, 'price_usd' => 7.32],
                ],
                'qty' => 100
            ],
            [
                'name' => 'Cortado',
                'name_kh' => 'ក័រតាដូ',
                'category_name' => 'Hot Coffee',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 12000, 'price_usd' => 2.93],
                    ['size' => 'M', 'price_khr' => 15000, 'price_usd' => 3.66],
                    ['size' => 'L', 'price_khr' => 18000, 'price_usd' => 4.39],
                    ['size' => 'XXL', 'price_khr' => 22000, 'price_usd' => 5.37],
                ],
                'qty' => 110
            ],
            [
                'name' => 'Ristretto',
                'name_kh' => 'រីស្ត្រេតតូ',
                'category_name' => 'Hot Coffee',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 10000, 'price_usd' => 2.44],
                    ['size' => 'M', 'price_khr' => 12000, 'price_usd' => 2.93],
                    ['size' => 'L', 'price_khr' => 15000, 'price_usd' => 3.66],
                    ['size' => 'XXL', 'price_khr' => 18000, 'price_usd' => 4.39],
                ],
                'qty' => 120
            ],
            // 🧊 ប្រភេទកាហ្វេទឹកកក (Iced Coffee)
            [
                'name' => 'Iced Americano',
                'name_kh' => 'អាមេរិកាណូទឹកកក',
                'category_name' => 'Iced Coffee',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 14000, 'price_usd' => 3.41],
                    ['size' => 'M', 'price_khr' => 17000, 'price_usd' => 4.15],
                    ['size' => 'L', 'price_khr' => 20000, 'price_usd' => 4.88],
                    ['size' => 'XXL', 'price_khr' => 25000, 'price_usd' => 6.10],
                ],
                'qty' => 180
            ],
            [
                'name' => 'Iced Latte',
                'name_kh' => 'ឡាតេទឹកកក',
                'category_name' => 'Iced Coffee',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 16000, 'price_usd' => 3.90],
                    ['size' => 'M', 'price_khr' => 19000, 'price_usd' => 4.63],
                    ['size' => 'L', 'price_khr' => 23000, 'price_usd' => 5.61],
                    ['size' => 'XXL', 'price_khr' => 28000, 'price_usd' => 6.83],
                ],
                'qty' => 160
            ],
            [
                'name' => 'Iced Mocha',
                'name_kh' => 'ម៉ូកាទឹកកក',
                'category_name' => 'Iced Coffee',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 17000, 'price_usd' => 4.15],
                    ['size' => 'M', 'price_khr' => 20000, 'price_usd' => 4.88],
                    ['size' => 'L', 'price_khr' => 25000, 'price_usd' => 6.10],
                    ['size' => 'XXL', 'price_khr' => 30000, 'price_usd' => 7.32],
                ],
                'qty' => 140
            ],
            [
                'name' => 'Iced Caramel Latte',
                'name_kh' => 'ឡាតេខារ៉ាមែលទឹកកក',
                'category_name' => 'Iced Coffee',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 18000, 'price_usd' => 4.39],
                    ['size' => 'M', 'price_khr' => 22000, 'price_usd' => 5.37],
                    ['size' => 'L', 'price_khr' => 26000, 'price_usd' => 6.34],
                    ['size' => 'XXL', 'price_khr' => 32000, 'price_usd' => 7.80],
                ],
                'qty' => 130
            ],
            [
                'name' => 'Cold Brew',
                'name_kh' => 'កាហ្វេចម្រាញ់ត្រជាក់',
                'category_name' => 'Iced Coffee',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 15000, 'price_usd' => 3.66],
                    ['size' => 'M', 'price_khr' => 18000, 'price_usd' => 4.39],
                    ['size' => 'L', 'price_khr' => 22000, 'price_usd' => 5.37],
                    ['size' => 'XXL', 'price_khr' => 27000, 'price_usd' => 6.59],
                ],
                'qty' => 150
            ],
            [
                'name' => 'Nitro Cold Brew',
                'name_kh' => 'កាហ្វេចម្រាញ់ត្រជាក់មានហ្គាស',
                'category_name' => 'Iced Coffee',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 18000, 'price_usd' => 4.39],
                    ['size' => 'M', 'price_khr' => 22000, 'price_usd' => 5.37],
                    ['size' => 'L', 'price_khr' => 26000, 'price_usd' => 6.34],
                    ['size' => 'XXL', 'price_khr' => 32000, 'price_usd' => 7.80],
                ],
                'qty' => 100
            ],
            // 🥛 កាហ្វេលាយជាមួយរសជាតិផ្សេងៗ (Flavored Coffee)
            [
                'name' => 'Vanilla Latte',
                'name_kh' => 'ឡាតេវ៉ានីឡា',
                'category_name' => 'Flavored Coffee',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 16000, 'price_usd' => 3.90],
                    ['size' => 'M', 'price_khr' => 19000, 'price_usd' => 4.63],
                    ['size' => 'L', 'price_khr' => 23000, 'price_usd' => 5.61],
                    ['size' => 'XXL', 'price_khr' => 28000, 'price_usd' => 6.83],
                ],
                'qty' => 140
            ],
            [
                'name' => 'Caramel Macchiato',
                'name_kh' => 'ម៉ាក្យាតូខារ៉ាមែល',
                'category_name' => 'Flavored Coffee',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 17000, 'price_usd' => 4.15],
                    ['size' => 'M', 'price_khr' => 20000, 'price_usd' => 4.88],
                    ['size' => 'L', 'price_khr' => 25000, 'price_usd' => 6.10],
                    ['size' => 'XXL', 'price_khr' => 30000, 'price_usd' => 7.32],
                ],
                'qty' => 130
            ],
            [
                'name' => 'Hazelnut Latte',
                'name_kh' => 'ឡាតេហេសែលណាត់',
                'category_name' => 'Flavored Coffee',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 16000, 'price_usd' => 3.90],
                    ['size' => 'M', 'price_khr' => 19000, 'price_usd' => 4.63],
                    ['size' => 'L', 'price_khr' => 23000, 'price_usd' => 5.61],
                    ['size' => 'XXL', 'price_khr' => 28000, 'price_usd' => 6.83],
                ],
                'qty' => 120
            ],
            [
                'name' => 'White Chocolate Mocha',
                'name_kh' => 'ម៉ូកាសូកូឡាស',
                'category_name' => 'Flavored Coffee',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 18000, 'price_usd' => 4.39],
                    ['size' => 'M', 'price_khr' => 22000, 'price_usd' => 5.37],
                    ['size' => 'L', 'price_khr' => 26000, 'price_usd' => 6.34],
                    ['size' => 'XXL', 'price_khr' => 32000, 'price_usd' => 7.80],
                ],
                'qty' => 110
            ],
            // 🍹 ភេសជ្ជៈផ្សេងៗក្នុងហាងកាហ្វេ (Non-Coffee Drinks)
            [
                'name' => 'Hot Chocolate',
                'name_kh' => 'សូកូឡាក្តៅ',
                'category_name' => 'Non-Coffee Drinks',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 12000, 'price_usd' => 2.93],
                    ['size' => 'M', 'price_khr' => 15000, 'price_usd' => 3.66],
                    ['size' => 'L', 'price_khr' => 18000, 'price_usd' => 4.39],
                    ['size' => 'XXL', 'price_khr' => 22000, 'price_usd' => 5.37],
                ],
                'qty' => 150
            ],
            [
                'name' => 'Iced Chocolate',
                'name_kh' => 'សូកូឡាទឹកកក',
                'category_name' => 'Non-Coffee Drinks',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 13000, 'price_usd' => 3.17],
                    ['size' => 'M', 'price_khr' => 16000, 'price_usd' => 3.90],
                    ['size' => 'L', 'price_khr' => 19000, 'price_usd' => 4.63],
                    ['size' => 'XXL', 'price_khr' => 23000, 'price_usd' => 5.61],
                ],
                'qty' => 140
            ],
            [
                'name' => 'Green Tea Latte',
                'name_kh' => 'តែបៃតងឡាតេ',
                'category_name' => 'Non-Coffee Drinks',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 13000, 'price_usd' => 3.17],
                    ['size' => 'M', 'price_khr' => 16000, 'price_usd' => 3.90],
                    ['size' => 'L', 'price_khr' => 19000, 'price_usd' => 4.63],
                    ['size' => 'XXL', 'price_khr' => 23000, 'price_usd' => 5.61],
                ],
                'qty' => 160
            ],
            [
                'name' => 'Matcha Latte',
                'name_kh' => 'ម៉ាចាឡាតេ',
                'category_name' => 'Non-Coffee Drinks',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 15000, 'price_usd' => 3.66],
                    ['size' => 'M', 'price_khr' => 18000, 'price_usd' => 4.39],
                    ['size' => 'L', 'price_khr' => 22000, 'price_usd' => 5.37],
                    ['size' => 'XXL', 'price_khr' => 27000, 'price_usd' => 6.59],
                ],
                'qty' => 140
            ],
            [
                'name' => 'Milk Tea',
                'name_kh' => 'តែទឹកដោះគោ',
                'category_name' => 'Non-Coffee Drinks',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 10000, 'price_usd' => 2.44],
                    ['size' => 'M', 'price_khr' => 13000, 'price_usd' => 3.17],
                    ['size' => 'L', 'price_khr' => 16000, 'price_usd' => 3.90],
                    ['size' => 'XXL', 'price_khr' => 20000, 'price_usd' => 4.88],
                ],
                'qty' => 200
            ],
            [
                'name' => 'Lemon Tea',
                'name_kh' => 'តែក្រូចឆ្មា',
                'category_name' => 'Non-Coffee Drinks',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 10000, 'price_usd' => 2.44],
                    ['size' => 'M', 'price_khr' => 13000, 'price_usd' => 3.17],
                    ['size' => 'L', 'price_khr' => 16000, 'price_usd' => 3.90],
                    ['size' => 'XXL', 'price_khr' => 20000, 'price_usd' => 4.88],
                ],
                'qty' => 180
            ],
            [
                'name' => 'Smoothie',
                'name_kh' => 'ស្មូទី',
                'category_name' => 'Non-Coffee Drinks',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 15000, 'price_usd' => 3.66],
                    ['size' => 'M', 'price_khr' => 18000, 'price_usd' => 4.39],
                    ['size' => 'L', 'price_khr' => 22000, 'price_usd' => 5.37],
                    ['size' => 'XXL', 'price_khr' => 27000, 'price_usd' => 6.59],
                ],
                'qty' => 150
            ],
            [
                'name' => 'Fruit Juice',
                'name_kh' => 'ទឹកផ្លែឈើ',
                'category_name' => 'Non-Coffee Drinks',
                'sizes' => [
                    ['size' => 'S', 'price_khr' => 12000, 'price_usd' => 2.93],
                    ['size' => 'M', 'price_khr' => 15000, 'price_usd' => 3.66],
                    ['size' => 'L', 'price_khr' => 18000, 'price_usd' => 4.39],
                    ['size' => 'XXL', 'price_khr' => 22000, 'price_usd' => 5.37],
                ],
                'qty' => 160
            ],
        ];

        foreach ($products as $productData) {
            $category = $categoryModels[$productData['category_name']];

            $product = Product::firstOrCreate([
                'name' => $productData['name'],
                'category_id' => $category->id,
            ], [
                'qty' => $productData['qty'],
                'image' => 'default-product.jpg'
            ]);

            // Create sizes for each product with both KHR and USD prices
            foreach ($productData['sizes'] as $sizeData) {
                ProductSize::firstOrCreate([
                    'product_id' => $product->id,
                    'size' => $sizeData['size'],
                ], [
                    'price' => $sizeData['price_khr'],
                    'price_khr' => $sizeData['price_khr'],
                    'price_usd' => $sizeData['price_usd'],
                ]);
            }
        }
    }
}
