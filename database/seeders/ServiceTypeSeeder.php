<?php

namespace Database\Seeders;

use App\Models\ServiceType;
use Illuminate\Database\Seeder;

class ServiceTypeSeeder extends Seeder
{
    public function run(): void
    {
        ServiceType::create([
            'name' => 'Điện',
            'price' => 2500.00,
            'unit' => 'kWh',
            'description' => 'Hóa đơn tiền điện sinh hoạt hàng tháng của phòng'
        ]);

        ServiceType::create([
            'name' => 'Nước',
            'price' => 10000.00,
            'unit' => 'm3',
            'description' => 'Hóa đơn nước sinh hoạt hàng tháng'
        ]);

        ServiceType::create([
            'name' => 'Mạng Internet',
            'price' => 150000.00,
            'unit' => 'Tháng',
            'description' => 'Dịch vụ mạng cáp quang tốc độ cao'
        ]);
    }
}
