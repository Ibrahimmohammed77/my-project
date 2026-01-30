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

    /**
     * نطاق الحصول على الخطط النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
