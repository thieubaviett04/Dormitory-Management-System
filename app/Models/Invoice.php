<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_code',
        'room_id',
        'student_id',
        'billing_month',
        'total_amount',
        'status',
        'paid_at',
        'payment_method'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'billing_month' => 'date'
    ];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
