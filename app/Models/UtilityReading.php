<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UtilityReading extends Model
{
    protected $fillable = [
        'room_id',
        'billing_month',
        'electricity_start',
        'electricity_end',
        'water_start',
        'water_end',
        'recorded_by'
    ];

    protected $casts = [
        'billing_month' => 'date',
        'electricity_start' => 'integer',
        'electricity_end' => 'integer',
        'water_start' => 'integer',
        'water_end' => 'integer',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
