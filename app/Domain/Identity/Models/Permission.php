<?php

namespace App\Domain\Identity\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $primaryKey = 'permission_id';
    protected $fillable = ['name', 'resource_type', 'action', 'description'];
    
    // The table has created_at but no updated_at
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;
    public $timestamps = true;

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id');
    }
}

