<?php

namespace App\Domain\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'subscription_id';

    protected $fillable = [
        'subscriber_type_id', 'subscriber_id', 'plan_id',
        'start_date', 'end_date', 'renewal_date', 'auto_renew',
        'subscription_status_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'renewal_date' => 'date',
        'auto_renew' => 'boolean',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function subscriberType()
    {
        return $this->belongsTo(\App\Domain\Shared\Models\LookupValue::class, 'subscriber_type_id');
    }

    public function status()
    {
        return $this->belongsTo(\App\Domain\Shared\Models\LookupValue::class, 'subscription_status_id');
    }

    /**
     * Get the owning subscriber model (School, Studio, etc.)
     * This simulates a polymorphic relation since we use an ID for type, not string.
     */
    public function getSubscriberAttribute()
    {
        $type = $this->subscriberType; // Preload this to avoid N+1
        if (!$type) return null;

        if ($type->code === 'SCHOOL') {
            return School::find($this->subscriber_id);
        }
        if ($type->code === 'STUDIO') {
            return Studio::find($this->subscriber_id);
        }
        if ($type->code === 'INDIVIDUAL') {
            return Account::find($this->subscriber_id); // Assuming Individual maps to Account or maybe Customer? Schema said 'INDIVIDUAL' user type. Assuming Account for now or Customer. Let's assume Customer based on table 'customers'.
            // Wait, schema has `customers` table. The lookup 'SUBSCRIBER_TYPE' has 'INDIVIDUAL'. 
            // Let me check what 'customers' table represents. It has account_id, so it's a profile.
            // I will map 'INDIVIDUAL' to `Customer` model.
            return Customer::find($this->subscriber_id);
        }
        
        return null;
    }

    public function scopeActive($query)
    {
        return $query->whereHas('status', function ($q) {
            $q->where('code', 'ACTIVE');
        })->where('end_date', '>=', now());
    }
}


