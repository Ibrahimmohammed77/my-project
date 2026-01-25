<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'account_id', 'first_name', 'last_name', 'date_of_birth', 
        'gender_id', 'email', 'phone', 'settings'
    ];

    protected $casts = [
        'settings' => 'array',
        'date_of_birth' => 'date',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function gender()
    {
        return $this->belongsTo(LookupValue::class, 'gender_id');
    }
}
