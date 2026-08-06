<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    // Cấp quyền cho phép lưu dữ liệu vào các cột này
    protected $fillable = [
        'student_code',
        'full_name',
        'email',
        'phone_number',
        'gender',
        'date_of_birth',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function roomRegistrations(): HasMany
    {
        return $this->hasMany(RoomRegistration::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }
}
