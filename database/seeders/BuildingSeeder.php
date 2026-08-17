<?php

namespace Database\Seeders;

use App\Models\Building;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Building::create([
            'code' => 'A1',
            'name' => 'Kí túc xá A1',
            'floors' => 10,
            'description' => 'Kí túc xá dành cho giảng viên và sinh viên ưu tú',
        ]);

        Building::create([
            'code' => 'A3',
            'name' => 'Kí túc xá A3',
            'floors' => 7,
            'description' => 'Khu nhà ở dành cho sinh viên nước ngoài',
        ]);

        Building::create([
            'code' => 'T1',
            'name' => 'Ký túc xá K1',
            'floors' => 6,
            'description' => 'Ký túc xá cao cấp đầy đủ tiện nghi',
        ]);
    }
}
