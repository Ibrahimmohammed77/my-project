<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Studio extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'studio_id';
    
    protected $fillable = [
        'account_id', 'name', 'description', 'logo', 'studio_status_id',
        'email', 'phone', 'website', 'address', 'city', 'country', 'settings'
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function status()
    {
        return $this->belongsTo(LookupValue::class, 'studio_status_id');
    }

    public function offices()
    {
        return $this->hasMany(Office::class, 'studio_id');
    }
}
