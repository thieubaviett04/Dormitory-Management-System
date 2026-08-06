<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Room extends Model
{
    protected $fillable = [
        'building_id',
        'room_number',
        'floor',
        'capacity',
        'status',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class);
    }

    public function roomRegistrations(): HasMany
    {
        return $this->hasMany(RoomRegistration::class);
    }

    public function allocations(): HasManyThrough
    {
        return $this->hasManyThrough(Allocation::class, Bed::class);
    }
}
