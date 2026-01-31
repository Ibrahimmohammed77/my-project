<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class LookupValue extends Model
{
    use HasFactory;

    protected $table = 'lookup_values';
    protected $primaryKey = 'lookup_value_id';

    protected $fillable = [
        'lookup_master_id',
        'code',
        'name',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = [
        'full_name',
    ];

    /**
     * علاقة الـ Master
     */
    public function master(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LookupMaster::class, 'lookup_master_id');
    }

    // ==================== SCOPES ====================

    /**
     * نطاق العناصر النشطة فقط
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * نطاق العناصر غير النشطة
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * نطاق الترتيب حسب الترتيب المحدد
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }

    /**
     * نطاق البحث
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * نطاق العناصر حسب Master Code
     */
    public function scopeByMasterCode(Builder $query, string $code): Builder
    {
        return $query->whereHas('master', function ($q) use ($code) {
            $q->where('code', $code);
        });
    }

    // ==================== ACCESSORS ====================

    /**
     * الحصول على الاسم الكامل (الكود + الاسم)
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->code} - {$this->name}";
    }

    /**
     * الحصول على حالة العنصر كنص
     */
    public function getStatusTextAttribute(): string
    {
        return $this->is_active ? 'نشط' : 'غير نشط';
    }

    // ==================== BUSINESS METHODS ====================

    /**
     * تفعيل العنصر
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * تعطيل العنصر
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * التحقق مما إذا كان العنصر يمكن حذفه
     */
    public function isDeletable(): bool
    {
        // يمكن إضافة شروط هنا مثل عدم وجود علاقات
        return true;
    }
}
