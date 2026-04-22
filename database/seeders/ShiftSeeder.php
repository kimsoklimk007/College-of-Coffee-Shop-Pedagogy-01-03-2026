<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shifts = [
            [
                'name' => 'Morning Shift',
                'name_kh' => 'វេនព្រឹក',
                'start_time' => '07:00:00',
                'end_time' => '15:00:00',
                'days_of_week' => json_encode([1,2,3,4,5]), // Monday to Friday
                'description' => 'Early morning shift for opening and breakfast service',
            ],
            [
                'name' => 'Regular Shift',
                'name_kh' => 'វេនធម្មតា',
                'start_time' => '08:00:00',
                'end_time' => '16:00:00',
                'days_of_week' => json_encode([1,2,3,4,5]), // Monday to Friday
                'description' => 'Standard business hours shift',
            ],
            [
                'name' => 'Afternoon Shift',
                'name_kh' => 'វេនល្ងាច',
                'start_time' => '12:00:00',
                'end_time' => '20:00:00',
                'days_of_week' => json_encode([1,2,3,4,5]), // Monday to Friday
                'description' => 'Afternoon shift covering lunch and dinner service',
            ],
            [
                'name' => 'Evening Shift',
                'name_kh' => 'វេនល្ងាចយប់',
                'start_time' => '16:00:00',
                'end_time' => '00:00:00',
                'days_of_week' => json_encode([1,2,3,4,5]), // Monday to Friday
                'description' => 'Evening shift for dinner service and closing',
            ],
            [
                'name' => 'Night Shift',
                'name_kh' => 'វេនយប',
                'start_time' => '22:00:00',
                'end_time' => '06:00:00',
                'days_of_week' => json_encode([1,2,3,4,5,6,7]), // All days
                'description' => 'Overnight shift for security and maintenance',
            ],
            [
                'name' => 'Weekend Shift',
                'name_kh' => 'វេនចុងសប្តាហ៍',
                'start_time' => '09:00:00',
                'end_time' => '18:00:00',
                'days_of_week' => json_encode([6,7]), // Saturday and Sunday
                'description' => 'Weekend shift with higher rates',
            ],
        ];

        foreach ($shifts as $shift) {
            Shift::create($shift);
        }
    }
}
