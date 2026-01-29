<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LookupMaster extends Model
{
    use HasFactory;

    protected $table = 'lookup_masters';
    protected $primaryKey = 'lookup_master_id';

    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    /**
     * علاقة القيم
     */
    public function values()
    {
        return $this->hasMany(LookupValue::class, 'lookup_master_id', 'lookup_master_id')
                    ->where('is_active', true)
                    ->orderBy('sort_order');
    }

    /**
     * الحصول على القيم النشطة فقط
     */
    public function activeValues()
    {
        return $this->hasMany(LookupValue::class, 'lookup_master_id', 'lookup_master_id')
                    ->where('is_active', true)
                    ->orderBy('sort_order');
    }
}