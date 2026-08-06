<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\ViolationStatus;

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

    protected $casts = [
        'record_date' => 'date',
        'status' => ViolationStatus::class,
    ];

    public function violationType()
    {
        return $this->belongsTo(ViolationType::class);
    }
}
