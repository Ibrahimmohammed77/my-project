<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscriber extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'subscriber_id';

    protected $fillable = ['account_id', 'subscriber_status_id', 'settings'];

    protected $casts = [
        'settings' => 'array',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function status()
    {
        return $this->belongsTo(LookupValue::class, 'subscriber_status_id');
    }
}
