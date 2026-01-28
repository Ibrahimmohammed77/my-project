<?php

namespace App\Domain\Identity\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Account extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $primaryKey = 'account_id';
    
    protected $fillable = [
        'username', 'email', 'full_name', 'phone', 'profile_image', 
        'account_status_id', 'account_type_id', 'password_hash'
    ];

    protected $hidden = [
        'password_hash', 'verification_code'
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function type()
    {
        return $this->belongsTo(\App\Domain\Shared\Models\LookupValue::class, 'account_type_id');
    }

    public function status()
    {
        return $this->belongsTo(\App\Domain\Shared\Models\LookupValue::class, 'account_status_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'account_roles', 'account_id', 'role_id');
    }
    
    public function studios()
    {
        return $this->hasMany(\App\Domain\Core\Models\Studio::class, 'account_id');
    }

    public function schools()
    {
        return $this->hasMany(\App\Domain\Core\Models\School::class, 'account_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeStatus($query, $statusCode)
    {
        return $query->whereHas('status', function ($q) use ($statusCode) {
            $q->where('code', $statusCode);
        });
    }

    public function scopeActive($query)
    {
        return $this->scopeStatus($query, 'ACTIVE');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    public function getIsActiveAttribute()
    {
        return $this->status && $this->status->code === 'ACTIVE';
    }
}


