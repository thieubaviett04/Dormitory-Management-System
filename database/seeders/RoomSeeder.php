<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buildingA1 = Building::where('code', 'A1')->first();
        $buildingA3 = Building::where('code', 'A3')->first();
        $buildingT1 = Building::where('code', 'T1')->first();

        if ($buildingA1) {
            Room::create([
                'building_id' => $buildingA1->id,
                'room_number' => '101',
                'floor' => 1,
                'capacity' => 4,
                'status' => 'available',
            ]);

            Room::create([
                'building_id' => $buildingA1->id,
                'room_number' => '102',
                'floor' => 1,
                'capacity' => 4,
                'status' => 'available',
            ]);

            Room::create([
                'building_id' => $buildingA1->id,
                'room_number' => '201',
                'floor' => 2,
                'capacity' => 6,
                'status' => 'available',
            ]);
        }

        if ($buildingA3) {
            Room::create([
                'building_id' => $buildingA3->id,
                'room_number' => '101',
                'floor' => 1,
                'capacity' => 4,
                'status' => 'available',
            ]);

            Room::create([
                'building_id' => $buildingA3->id,
                'room_number' => '102',
                'floor' => 1,
                'capacity' => 4,
                'status' => 'maintenance',
            ]);
        }

        if ($buildingT1) {
            Room::create([
                'building_id' => $buildingT1->id,
                'room_number' => '101',
                'floor' => 1,
                'capacity' => 2,
                'status' => 'full',
            ]);
        }
    }
}
