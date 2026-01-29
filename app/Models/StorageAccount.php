<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorageAccount extends Model
{
    use HasFactory;

    protected $table = 'storage_accounts';
    protected $primaryKey = 'storage_account_id';

    protected $fillable = [
        'owner_type',
        'owner_id',
        'total_space',
        'used_space',
        'status',
    ];

    protected $casts = [
        'total_space' => 'integer',
        'used_space' => 'integer',
    ];

    /**
     * علاقة متعددة الأشكال للمالك
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /**
     * الحصول على المساحة المتاحة
     */
    public function getAvailableSpaceAttribute()
    {
        return $this->total_space - $this->used_space;
    }

    /**
     * الحصول على نسبة الاستخدام
     */
    public function getUsagePercentageAttribute()
    {
        if ($this->total_space == 0) {
            return 0;
        }

        return ($this->used_space / $this->total_space) * 100;
    }

    /**
     * التحقق مما إذا كانت المساحة كافية
     */
    public function hasSpace($size)
    {
        return $this->available_space >= $size;
    }

    /**
     * زيادة المساحة المستخدمة
     */
    public function incrementUsedSpace($size)
    {
        $this->increment('used_space', $size);
    }

    /**
     * تقليل المساحة المستخدمة
     */
    public function decrementUsedSpace($size)
    {
        $this->decrement('used_space', $size);
    }

    /**
     * تحديث المساحة الكلية
     */
    public function updateTotalSpace($newTotal)
    {
        $this->update(['total_space' => $newTotal]);
    }

    /**
     * نطاق الحسابات النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
