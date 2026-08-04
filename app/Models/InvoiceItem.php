<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'service_type_id',
        'item_name',
        'quantity',
        'price',
        'subtotal'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }
}
