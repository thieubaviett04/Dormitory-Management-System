<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViolationRecord extends Model
{
    protected $fillable = [
        'student_id',
        'violation_type_id',
        'record_date',
        'description',
        'recorded_by',
        'status'
    ];

    public function violationType()
    {
        return $this->belongsTo(ViolationType::class);
    }
}
