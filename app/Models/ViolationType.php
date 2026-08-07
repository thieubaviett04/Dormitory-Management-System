<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViolationType extends Model
{
    protected $fillable = [
        'name',
        'severity',
        'fine_amount',
        'description'
    ];

    public function violationRecords()
    {
        return $this->hasMany(ViolationRecord::class);
    }
}
