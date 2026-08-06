<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    // Cấp quyền cho phép lưu dữ liệu vào các cột này
    protected $fillable = [
        'student_code',
        'full_name',
        'email',
        'phone_number',
        'gender',
        'date_of_birth'
    ];
}
