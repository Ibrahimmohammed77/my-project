<?php

namespace App\Domain\Shared\Models;

use Illuminate\Database\Eloquent\Model;

class LookupValue extends Model
{
    protected $primaryKey = 'lookup_value_id';
    protected $fillable = ['lookup_master_id', 'code', 'name', 'description', 'is_active', 'sort_order'];

    public function master()
    {
        return $this->belongsTo(LookupMaster::class, 'lookup_master_id');
    }

    /**
     * Scope a query to only include values of a given master category code.
     */
    public function scopeCategory($query, $code)
    {
        return $query->whereHas('master', function ($q) use ($code) {
            $q->where('code', $code);
        });
    }

    /**
     * Scope a query to only include active values.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to find a specific value code within a category.
     */
    public function scopeByCode($query, $categoryCode, $valueCode)
    {
        return $query->category($categoryCode)->where('code', $valueCode);
    }
}

