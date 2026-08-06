<?php

namespace Database\Seeders;

use App\Models\Bed;
use App\Models\Room;
use Illuminate\Database\Seeder;

class BedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = Room::all();

        foreach ($rooms as $room) {
            for ($i = 1; $i <= $room->capacity; $i++) {
                $status = 'available';
                if ($room->status === 'maintenance') {
                    $status = 'maintenance';
                }

                Bed::create([
                    'room_id' => $room->id,
                    'bed_number' => 'G'.$i,
                    'status' => $status,
                ]);
            }
        }
    }
}
