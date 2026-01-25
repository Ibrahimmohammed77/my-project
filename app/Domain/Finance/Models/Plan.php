<?php

namespace App\Domain\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'plan_id';

    protected $fillable = [
        'name', 'description', 'storage_limit', 'price_monthly', 'price_yearly',
        'max_albums', 'max_cards', 'max_users', 'max_offices',
        'features', 'billing_cycle_id', 'is_active'
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    public function billingCycle()
    {
        return $this->belongsTo(\App\Domain\Shared\Models\LookupValue::class, 'billing_cycle_id');
    }
}


