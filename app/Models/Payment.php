<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'invoice_id',
        'amount',
        'payment_method_id',
        'gateway_transaction_id',
        'gateway_response',
        'payment_status_id',
        'paid_at',
        'refunded_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
        'gateway_response' => 'array',
    ];

    /**
     * علاقة الفاتورة
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
    }

    /**
     * علاقة طريقة الدفع من القيم المحددة
     */
    public function method()
    {
        return $this->belongsTo(LookupValue::class, 'payment_method_id', 'lookup_value_id');
    }

    /**
     * علاقة حالة الدفع من القيم المحددة
     */
    public function status()
    {
        return $this->belongsTo(LookupValue::class, 'payment_status_id', 'lookup_value_id');
    }

    /**
     * التحقق مما إذا كان الدفع ناجحاً
     */
    public function getIsSuccessfulAttribute()
    {
        return $this->status && $this->status->code === 'completed';
    }

    /**
     * التحقق مما إذا كان الدفع مرفوضاً
     */
    public function getIsFailedAttribute()
    {
        return $this->status && $this->status->code === 'failed';
    }

    /**
     * نطاق المدفوعات الناجحة
     */
    public function scopeSuccessful($query)
    {
        return $query->whereHas('status', function($q) {
            $q->where('code', 'completed');
        });
    }

    /**
     * نطاق المدفوعات المعلقة
     */
    public function scopePending($query)
    {
        return $query->whereHas('status', function($q) {
            $q->where('code', 'pending');
        });
    }

    /**
     * تحديث حالة الدفع
     */
    public function markAsCompleted($gatewayData = null)
    {
        $status = LookupValue::where('code', 'completed')->first();

        $this->update([
            'payment_status_id' => $status->lookup_value_id,
            'paid_at' => now(),
            'gateway_response' => $gatewayData,
        ]);

        // تحديث المبلغ المدفوع في الفاتورة
        $this->invoice->update([
            'paid_amount' => $this->invoice->paid_amount + $this->amount
        ]);
    }
}
