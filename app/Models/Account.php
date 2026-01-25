<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Account extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $primaryKey = 'account_id';
    
    protected $fillable = [
        'username', 'email', 'full_name', 'phone', 'profile_image', 
        'account_status_id', 'password_hash'
    ];

    protected $hidden = [
        'password_hash', 'verification_code'
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function status()
    {
        return $this->belongsTo(LookupValue::class, 'account_status_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'account_roles', 'account_id', 'role_id');
    }
    
    public function studios()
    {
        return $this->hasMany(Studio::class, 'account_id');
    }

    public function schools()
    {
        return $this->hasMany(School::class, 'account_id');
    }
}
