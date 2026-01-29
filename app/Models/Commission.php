<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;

    protected $table = 'commissions';
    protected $primaryKey = 'commission_id';

    protected $fillable = [
        'studio_id',
        'storage_library_id',
        'invoice_id',
        'transaction_type_id',
        'amount',
        'commission_rate',
        'studio_share',
        'platform_share',
        'settlement_date',
        'commission_status_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'studio_share' => 'decimal:2',
        'platform_share' => 'decimal:2',
        'settlement_date' => 'date',
    ];

    /**
     * علاقة الاستوديو
     */
    public function studio()
    {
        return $this->belongsTo(Studio::class, 'studio_id', 'studio_id');
    }

    /**
     * علاقة مكتبة التخزين
     */
    public function storageLibrary()
    {
        return $this->belongsTo(StorageLibrary::class, 'storage_library_id', 'storage_library_id');
    }

    /**
     * علاقة الفاتورة
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
    }

    /**
     * علاقة نوع المعاملة من القيم المحددة
     */
    public function transactionType()
    {
        return $this->belongsTo(LookupValue::class, 'transaction_type_id', 'lookup_value_id');
    }

    /**
     * علاقة حالة العمولة من القيم المحددة
     */
    public function status()
    {
        return $this->belongsTo(LookupValue::class, 'commission_status_id', 'lookup_value_id');
    }

    /**
     * نطاق العمولات المستحقة الدفع
     */
    public function scopePending($query)
    {
        return $query->where('commission_status_id', function($query) {
            $query->select('lookup_value_id')
                  ->from('lookup_values')
                  ->where('code', 'pending')
                  ->limit(1);
        });
    }

    /**
     * نطاق العمولات المسددة
     */
    public function scopePaid($query)
    {
        return $query->where('commission_status_id', function($query) {
            $query->select('lookup_value_id')
                  ->from('lookup_values')
                  ->where('code', 'paid')
                  ->limit(1);
        });
    }

    /**
     * حساب حصة الاستوديو والمنصة
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($commission) {
            $commission->studio_share = $commission->amount * ($commission->commission_rate / 100);
            $commission->platform_share = $commission->amount - $commission->studio_share;
        });

        static::updating(function ($commission) {
            if ($commission->isDirty('amount') || $commission->isDirty('commission_rate')) {
                $commission->studio_share = $commission->amount * ($commission->commission_rate / 100);
                $commission->platform_share = $commission->amount - $commission->studio_share;
            }
        });
    }
}
