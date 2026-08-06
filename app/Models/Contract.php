<?php

namespace App\Models;

use App\Enums\ContractStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Contract extends Model
{
    protected $fillable = [
        'contract_code',
        'room_registration_id',
        'student_id',
        'start_date',
        'end_date',
        'monthly_rate',
        'status',
        'signed_at',
        'terminated_at',
        'termination_reason',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'monthly_rate' => 'decimal:2',
            'status' => ContractStatus::class,
            'signed_at' => 'datetime',
            'terminated_at' => 'datetime',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ContractStatus::Active->value);
    }

    public function roomRegistration(): BelongsTo
    {
        return $this->belongsTo(RoomRegistration::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(Allocation::class);
    }

    public function currentAllocation(): HasOne
    {
        return $this->hasOne(Allocation::class)
            ->whereNull('released_at')
            ->latestOfMany('allocated_at');
    }

    public function renewals(): HasMany
    {
        return $this->hasMany(ContractRenewal::class);
    }
}
