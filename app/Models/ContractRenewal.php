<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractRenewal extends Model
{
    protected $fillable = [
        'contract_id',
        'previous_end_date',
        'new_end_date',
        'renewed_at',
        'renewed_by',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'previous_end_date' => 'date',
            'new_end_date' => 'date',
            'renewed_at' => 'datetime',
        ];
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function renewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'renewed_by');
    }
}
