<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $table = 'permissions';
    protected $primaryKey = 'permission_id';

    protected $fillable = [
        'name',
        'resource_type',
        'action',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * علاقة الأدوار التي لديها هذه الصلاحية
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id')
                    ->withPivot('is_active')
                    ->withTimestamps();
    }

    /**
     * نطاق الحصول على الصلاحيات النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * نطاق البحث حسب نوع المورد
     */
    public function scopeByResource($query, $resourceType)
    {
        return $query->where('resource_type', $resourceType);
    }

    /**
     * نطاق البحث حسب الإجراء
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }
}
