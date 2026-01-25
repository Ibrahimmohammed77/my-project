<?php

namespace App\Domain\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'school_id';

    protected $fillable = [
        'account_id', 'name', 'description', 'logo', 
        'school_type_id', 'school_level_id', 'school_status_id',
        'email', 'phone', 'website', 'address', 'city', 'country', 'settings'
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function account()
    {
        return $this->belongsTo(\App\Domain\Identity\Models\Account::class, 'account_id');
    }

    public function type()
    {
        return $this->belongsTo(\App\Domain\Shared\Models\LookupValue::class, 'school_type_id');
    }

    public function level()
    {
        return $this->belongsTo(\App\Domain\Shared\Models\LookupValue::class, 'school_level_id');
    }

    public function status()
    {
        return $this->belongsTo(\App\Domain\Shared\Models\LookupValue::class, 'school_status_id');
    }

    public function scopeStatus($query, $statusCode)
    {
        return $query->whereHas('status', function ($q) use ($statusCode) {
            $q->where('code', $statusCode);
        });
    }

    public function scopeType($query, $typeCode)
    {
        return $query->whereHas('type', function ($q) use ($typeCode) {
            $q->where('code', $typeCode); 
        });
    }

    public function scopeActive($query)
    {
        return $this->scopeStatus($query, 'ACTIVE');
    }

    public function getIsActiveAttribute()
    {
        return $this->status && $this->status->code === 'ACTIVE';
    }
}


