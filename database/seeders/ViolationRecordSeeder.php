<?php

namespace Database\Seeders;

use App\Models\ViolationRecord;
use Illuminate\Database\Seeder;

class ViolationRecordSeeder extends Seeder
{
    public function run(): void
    {
        ViolationRecord::create([
            'student_id' => 10,
            'violation_type_id' => 2, // Lỗi "Nấu ăn sai quy định"
            'record_date' => '2026-08-02',
            'description' => 'Sử dụng bếp điện đun nấu mì tôm trong phòng ngủ lúc 20:30, cán bộ phòng trực phát hiện.',
            'recorded_by' => 2,
            'status' => 'pending'
        ]);

        ViolationRecord::create([
            'student_id' => 11,
            'violation_type_id' => 3, // Lỗi "Về muộn"
            'record_date' => '2026-08-03',
            'description' => 'Trở về KTX lúc 23:45, bảo vệ ghi nhận vi phạm.',
            'recorded_by' => 3,
            'status' => 'resolved'
        ]);
    }
}
