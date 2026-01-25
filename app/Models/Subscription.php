<?php

namespace App\Models;

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
        return $this->belongsTo(LookupValue::class, 'subscriber_type_id');
    }

    public function status()
    {
        return $this->belongsTo(LookupValue::class, 'subscription_status_id');
    }
    
    // Note: polymorphic relation for subscriber_id is complex because type is stored as ID lookup, not Class Name.
    // Helper accessors might be needed or a custom relationship.
    // For standard polymorphic, Laravel expects a string mapping. 
    // We can manually define accessor or just keep it as ID.
}
