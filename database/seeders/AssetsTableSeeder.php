<?php

namespace Database\Seeders;

use App\Models\Asset;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AssetsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Asset::factory()->count(10)->create();
    }
}
