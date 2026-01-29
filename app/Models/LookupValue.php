<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * علاقة الـ Master
     */
    public function master()
    {
        return $this->belongsTo(LookupMaster::class, 'lookup_master_id', 'lookup_master_id');
    }

    /**
     * نطاق الحصول على العناصر النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * نطاق الترتيب حسب الترتيب المحدد
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}