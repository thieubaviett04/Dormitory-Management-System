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
    ];
}
