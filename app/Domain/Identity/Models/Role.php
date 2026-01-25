<?php

namespace App\Domain\Identity\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $primaryKey = 'role_id';
    protected $fillable = ['name', 'description', 'is_system'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id');
    }

    public function accounts()
    {
        return $this->belongsToMany(Account::class, 'account_roles', 'role_id', 'account_id');
    }
}

