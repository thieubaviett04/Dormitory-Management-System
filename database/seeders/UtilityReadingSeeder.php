<?php

namespace Database\Seeders;

use App\Models\UtilityReading;
use Illuminate\Database\Seeder;

class UtilityReadingSeeder extends Seeder
{
    public function run(): void
    {
        // Ghi nhận số điện nước Tháng 8/2026 cho Phòng số 1
        UtilityReading::create([
            'room_id' => 1,
            'billing_month' => '2026-08-01',
            'electricity_start' => 1200,
            'electricity_end' => 1350, // Tiêu thụ 150 kWh
            'water_start' => 250,
            'water_end' => 262, // Tiêu thụ 12 m3
            'recorded_by' => 2
        ]);

        // Ghi nhận số điện nước Tháng 8/2026 cho Phòng số 2
        UtilityReading::create([
            'room_id' => 2,
            'billing_month' => '2026-08-01',
            'electricity_start' => 800,
            'electricity_end' => 920, // Tiêu thụ 120 kWh
            'water_start' => 150,
            'water_end' => 158, // Tiêu thụ 8 m3
            'recorded_by' => 2
        ]);
    }
}
