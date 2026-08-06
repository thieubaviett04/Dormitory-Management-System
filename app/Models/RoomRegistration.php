<?php

namespace App\Models;

use App\Enums\RoomRegistrationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomRegistration extends Model
{
    // Cấp quyền cho phép lưu dữ liệu vào các cột này
    protected $fillable = [
        'student_id',
        'room_id',
        'status',
        'registered_at',
        'reviewed_at',
        'reviewed_by',
        'rejected_reason',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'status' => RoomRegistrationStatus::class,
            'registered_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', RoomRegistrationStatus::activeValues());
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
