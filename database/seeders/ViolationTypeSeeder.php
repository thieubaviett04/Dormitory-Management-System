<?php

namespace Database\Seeders;

use App\Models\ViolationType;
use Illuminate\Database\Seeder;

class ViolationTypeSeeder extends Seeder
{
    public function run(): void
    {
        ViolationType::create([
            'name' => 'Hút thuốc trong khuôn viên KTX',
            'severity' => 'high',
            'fine_amount' => 500000.00,
            'description' => 'Vi phạm quy định phòng chống cháy nổ và sức khỏe cộng đồng'
        ]);

        ViolationType::create([
            'name' => 'Nấu ăn sai nơi quy định',
            'severity' => 'medium',
            'fine_amount' => 200000.00,
            'description' => 'Sử dụng bếp điện, bếp ga mini đun nấu trong phòng ngủ'
        ]);

        ViolationType::create([
            'name' => 'Về muộn sau giờ nghiêm quân (23:00)',
            'severity' => 'low',
            'fine_amount' => 50000.00,
            'description' => 'Sinh viên ra ngoài và trở về phòng sau 23:00 không có lý do chính đáng'
        ]);
    }
}
