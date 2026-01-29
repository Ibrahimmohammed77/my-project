<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $table = 'plans';
    protected $primaryKey = 'plan_id';

    protected $fillable = [
        'name',
        'description',
        'storage_limit',
        'price_monthly',
        'price_yearly',
        'max_albums',
        'max_cards',
        'max_users',
        'max_storage_libraries',
        'features',
        'billing_cycle_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'features' => 'array',
        'storage_limit' => 'integer',
        'max_albums' => 'integer',
        'max_cards' => 'integer',
        'max_users' => 'integer',
        'max_storage_libraries' => 'integer',
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
    ];

    /**
     * علاقة دورة الفاتورة من القيم المحددة
     */
    public function billingCycle()
    {
        return $this->belongsTo(LookupValue::class, 'billing_cycle_id', 'lookup_value_id');
    }

    /**
     * علاقة الاشتراكات على هذا الخطة
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'plan_id', 'plan_id');
    }

    /**
     * نطاق الحصول على الخطط النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * نطاق الحصول على الخطط الشهرية
     */
    public function scopeMonthly($query)
    {
        return $query->where('billing_cycle_id', function($query) {
            $query->select('lookup_value_id')
                  ->from('lookup_values')
                  ->where('code', 'monthly')
                  ->limit(1);
        });
    }

    /**
     * نطاق الحصول على الخطط السنوية
     */
    public function scopeYearly($query)
    {
        return $query->where('billing_cycle_id', function($query) {
            $query->select('lookup_value_id')
                  ->from('lookup_values')
                  ->where('code', 'yearly')
                  ->limit(1);
        });
    }
}
