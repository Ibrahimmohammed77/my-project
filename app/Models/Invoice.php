<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';
    protected $primaryKey = 'invoice_id';

    protected $fillable = [
        'invoice_number',
        'user_id',
        'plan_id',
        'issue_date',
        'due_date',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'currency',
        'invoice_status_id',
        'payment_method_id',
        'payment_date',
        'transaction_id',
        'notes',
        'pdf_path',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'payment_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    /**
     * علاقة المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * علاقة الخطة
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id', 'plan_id');
    }

    /**
     * علاقة حالة الفاتورة من القيم المحددة
     */
    public function status()
    {
        return $this->belongsTo(LookupValue::class, 'invoice_status_id', 'lookup_value_id');
    }

    /**
     * علاقة طريقة الدفع من القيم المحددة
     */
    public function paymentMethod()
    {
        return $this->belongsTo(LookupValue::class, 'payment_method_id', 'lookup_value_id');
    }

    /**
     * علاقة بنود الفاتورة
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'invoice_id');
    }

    /**
     * علاقة المدفوعات
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_id', 'invoice_id');
    }

    /**
     * علاقة العمولات
     */
    public function commissions()
    {
        return $this->hasMany(Commission::class, 'invoice_id', 'invoice_id');
    }

    /**
     * التحقق مما إذا كانت الفاتورة مدفوعة بالكامل
     */
    public function getIsPaidAttribute()
    {
        return $this->paid_amount >= $this->total_amount;
    }

    /**
     * الحصول على المبلغ المتبقي
     */
    public function getRemainingAmountAttribute()
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }

    /**
     * نطاق الفواتير المستحقة
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('paid_amount', '<', DB::raw('total_amount'));
    }

    /**
     * نطاق الفواتير المعلقة
     */
    public function scopePending($query)
    {
        return $query->whereHas('status', function($q) {
            $q->where('code', 'pending');
        });
    }
}
