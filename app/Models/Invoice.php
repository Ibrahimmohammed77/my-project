<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $primaryKey = 'invoice_id';

    protected $fillable = [
        'invoice_number', 'subscriber_type_id', 'subscriber_id',
        'issue_date', 'due_date', 'subtotal', 'tax_amount', 'discount_amount',
        'total_amount', 'paid_amount', 'currency',
        'invoice_status_id', 'payment_method_id', 'payment_date',
        'transaction_id', 'notes', 'pdf_path'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'payment_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }

    public function status()
    {
        return $this->belongsTo(LookupValue::class, 'invoice_status_id');
    }
    
    public function paymentMethod()
    {
         return $this->belongsTo(LookupValue::class, 'payment_method_id');
    }
}
