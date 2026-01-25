<?php

namespace App\Models;

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
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function type()
    {
        return $this->belongsTo(LookupValue::class, 'school_type_id');
    }

    public function level()
    {
        return $this->belongsTo(LookupValue::class, 'school_level_id');
    }

    public function status()
    {
        return $this->belongsTo(LookupValue::class, 'school_status_id');
    }
}
