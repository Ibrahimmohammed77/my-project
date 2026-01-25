<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'invoice_id', 'amount', 'payment_method_id', 
        'gateway_transaction_id', 'gateway_response',
        'payment_status_id', 'paid_at', 'refunded_at'
    ];

    protected $casts = [
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function method()
    {
        return $this->belongsTo(LookupValue::class, 'payment_method_id');
    }

    public function status()
    {
        return $this->belongsTo(LookupValue::class, 'payment_status_id');
    }
}
