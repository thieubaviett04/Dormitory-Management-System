<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Bed;
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
                // Đặt một số giường là occupied để dữ liệu sinh động
                $status = 'available';
                if ($room->status === 'full') {
                    $status = 'occupied';
                } elseif ($room->status === 'maintenance') {
                    $status = 'maintenance';
                } elseif ($i === 2) {
                    $status = 'occupied';
                }

                Bed::create([
                    'room_id' => $room->id,
                    'bed_number' => 'G' . $i,
                    'status' => $status,
                ]);
            }
        }
    }
}
