<?php

namespace Database\Seeders;

use App\Models\ViolationRecord;
use Illuminate\Database\Seeder;

class ViolationRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ViolationRecord::create([
            'student_id' => 10,
            'violation_type_id' => 1,
            'record_date' => '2026-08-05',
            'description' => 'Hút thuốc ở sảnh hành lang tầng 2',
            'recorded_by' => 2,
            'status' => 'pending',
        ]);

        ViolationRecord::create([
            'student_id' => 11,
            'violation_type_id' => 2,
            'record_date' => '2026-08-05',
            'description' => 'Sử dụng bếp từ nấu lẩu trong phòng ngủ',
            'recorded_by' => 2,
            'status' => 'resolved',
        ]);
    }
}
