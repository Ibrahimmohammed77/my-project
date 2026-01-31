<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
        'features',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'features' => 'array',
        'storage_limit' => 'integer',
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
    ];

    protected $appends = [
        'formatted_price_monthly',
        'formatted_price_yearly',
        'storage_limit_gb',
    ];

    // ==================== SCOPES ====================

    /**
     * نطاق الخطط النشطة فقط
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * نطاق الخطط غير النشطة
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * نطاق البحث
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * نطاق الفرز حسب السعر
     */
    public function scopeOrderByPrice(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy('price_monthly', $direction);
    }

    /**
     * نطاق التحميل المبكر للعلاقات
     */
    public function scopeWithCommonRelations(Builder $query): Builder
    {
        return $query; // يمكن إضافة العلاقات هنا إذا وجدت
    }

    // ==================== ACCESSORS ====================

    /**
     * الحصول على السعر الشهري المنسق
     */
    public function getFormattedPriceMonthlyAttribute(): string
    {
        return number_format($this->price_monthly, 2);
    }

    /**
     * الحصول على السعر السنوي المنسق
     */
    public function getFormattedPriceYearlyAttribute(): string
    {
        return number_format($this->price_yearly, 2);
    }

    /**
     * الحصول على حد التخزين بالجيجابايت
     */
    public function getStorageLimitGbAttribute(): float
    {
        return $this->storage_limit / 1024;
    }

    /**
     * التحقق مما إذا كانت الخطة مجانية
     */
    public function getIsFreeAttribute(): bool
    {
        return $this->price_monthly == 0 && $this->price_yearly == 0;
    }

    // ==================== BUSINESS METHODS ====================

    /**
     * تفعيل الخطة
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * تعطيل الخطة
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * التحقق مما إذا كانت الخطة تحتوي على ميزة معينة
     */
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    /**
     * إضافة ميزة جديدة
     */
    public function addFeature(string $feature): bool
    {
        $features = $this->features ?? [];
        if (!in_array($feature, $features)) {
            $features[] = $feature;
            return $this->update(['features' => $features]);
        }

        return false;
    }

    /**
     * إزالة ميزة
     */
    public function removeFeature(string $feature): bool
    {
        $features = $this->features ?? [];
        $key = array_search($feature, $features);

        if ($key !== false) {
            unset($features[$key]);
            return $this->update(['features' => array_values($features)]);
        }

        return false;
    }
}
