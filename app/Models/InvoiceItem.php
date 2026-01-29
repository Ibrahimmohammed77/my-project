<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'invoice_items';
    protected $primaryKey = 'item_id';

    protected $fillable = [
        'invoice_id',
        'description',
        'quantity',
        'unit_price',
        'total_price',
        'item_type_id',
        'related_id',
        'taxable',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'taxable' => 'boolean',
    ];

    /**
     * علاقة الفاتورة
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
    }

    /**
     * علاقة نوع البند من القيم المحددة
     */
    public function type()
    {
        return $this->belongsTo(LookupValue::class, 'item_type_id', 'lookup_value_id');
    }

    /**
     * حساب السعر الإجمالي
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            if (empty($item->total_price) || $item->isDirty(['quantity', 'unit_price'])) {
                $item->total_price = $item->quantity * $item->unit_price;
            }
        });
    }

    /**
     * علاقة مرنة مع الجهة ذات الصلة (مثل اشتراك، إلخ)
     */
    public function related()
    {
        // يمكنك تخصيص هذه العلاقة حسب أنواع البنود
        $type = $this->type->code ?? '';

        switch ($type) {
            case 'subscription':
                return $this->belongsTo(Subscription::class, 'related_id', 'subscription_id');
            case 'storage':
                return $this->belongsTo(StorageLibrary::class, 'related_id', 'storage_library_id');
            default:
                return null;
        }
    }
}
