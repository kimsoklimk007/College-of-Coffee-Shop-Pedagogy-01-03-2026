<?php
namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
// use Database\Seeders\OrderSeeder;
use Database\Seeders\DeliveryLocationSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\DiscountSeeder;
use Database\Seeders\EmployeeRoleSeeder;
use Database\Seeders\ShiftSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::updateOrCreate([
            'email' => 'admin@gmail.com',
        ],
            [
                'name'     => 'Admin',
                'password' => Hash::make(env('DEFAULT_USER_PASSWORD', 'Password123')),
                'provider' => 'simple',
                'role'     => 'admin',
                'status'   => 'Active',
            ]
        );

        $this->call([
            // Super Admin (System Owner) - must be first
            SuperAdminSeeder::class,
            // Employee Management
            EmployeeRoleSeeder::class,
            ShiftSeeder::class,
            // Delivery
            DeliveryLocationSeeder::class,
            // Products
            ProductSeeder::class,
            // Discounts
            DiscountSeeder::class,

        ]);

    }
}
