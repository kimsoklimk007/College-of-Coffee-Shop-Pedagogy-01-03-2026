<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'kim.soklim@kingmu.ksm.kh'],
            [
                'name' => 'KIM SOKLIM',
                'username' => 'kimsoklim',
                'phone' => '0978641000',
                'password' => Hash::make('kimsoklim@@@@1998(KING_MU)@@'),
                'role' => 'super_admin',
                'shop_id' => null,
                'status' => 'Active',
                'provider' => 'simple',
            ]
        );
    }
}
