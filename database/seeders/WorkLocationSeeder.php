<?php

/**
 * WorkLocationSeeder - បង្កើតទីតាំងធ្វើការដំបូង
 * 
 * Seeder នេះបង្កើតទីតាំងធ្វើការគោលសម្រាប់ប្រព័ន្ធ
 * វាបង្កើតហាងស្នាក់ការកណ្ដាលដោយស្វ័យប្រវត្តិ
 */

namespace Database\Seeders;

use App\Models\WorkLocation;
use Illuminate\Database\Seeder;

class WorkLocationSeeder extends Seeder
{
    /**
     * បង្កើតទីតាំងធ្វើការគោល
     */
    public function run(): void
    {
        // ពិនិត្យថាតើមានទីតាំងគោលរួចហើយឬអត់
        if (WorkLocation::where('is_default', true)->exists()) {
            $this->command->info('ទីតាំងគោលមានរួចហើយ');
            return;
        }

        // បង្កើតហាងស្នាក់ការកណ្ដាល
        WorkLocation::create([
            'location_code' => 'MAIN001',
            'name' => 'Main Coffee Shop',
            'name_kh' => 'ហាងកាហ្វេស្នាក់ការកណ្ដាល',
            'description' => 'ហាងកាហ្វេស្នាក់ការកណ្ដាលនៃប្រព័ន្ធ POS',
            'address' => '123 Main Street',
            'city' => 'Phnom Penh',
            'province' => 'Phnom Penh',
            'location_type' => 'main_shop',
            
            // GPS coordinates for Phnom Penh (example)
            'latitude' => 11.5564,
            'longitude' => 104.9282,
            'radius_meters' => 100, // កម្រាស់អនុញ្ញាត 100 ម៉ែត្រ
            
            // Security settings
            'requires_wifi_ssid' => false,
            'allowed_wifi_ssid' => null,
            'requires_bluetooth' => false,
            
            // Status
            'status' => 'active',
            'is_default' => true,
            
            // Working hours
            'opening_time' => '06:00:00',
            'closing_time' => '22:00:00',
            'working_days' => [1, 2, 3, 4, 5, 6, 7], // ថ្ងៃអាទិត្យ-សៅរ៍
            
            'notes' => 'ទីតាំងគោលត្រូវបានបង្កើតដោយស្វ័យប្រវត្តិ'
        ]);

        $this->command->info('បានបង្កើតទីតាំងគោលដោយជោគជ័យ');
    }
}
