<?php

namespace App\Domain\Finance\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $primaryKey = 'item_id';

    protected $fillable = [
        'invoice_id', 'description', 'quantity', 'unit_price', 'total_price',
        'item_type_id', 'related_id', 'taxable'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function type()
    {
        return $this->belongsTo(\App\Domain\Shared\Models\LookupValue::class, 'item_type_id');
    }
}


