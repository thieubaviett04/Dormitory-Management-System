<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomRegistration extends Model
{
    // Cấp quyền cho phép lưu dữ liệu vào các cột này
    protected $fillable = [
        'student_id',
        'room_id',
        'status',
        'registered_at'
    ];
}
