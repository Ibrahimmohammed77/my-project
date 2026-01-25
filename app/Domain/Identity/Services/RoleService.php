<?php

namespace App\Domain\Identity\Services;

use App\Domain\Identity\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class RoleService
{
    protected $repository;

    public function __construct(RoleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    public function update($id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }
    
    public function delete($id): bool
    {
        // منع حذف الأدوار النظامية
        $role = $this->find($id);
        if ($role && $role->is_system) {
            return false;
        }

        return $this->repository->delete($id);
    }
    
    public function find($id): ?Model
    {
        return $this->repository->find($id);
    }

    /**
     * تعيين صلاحية لدور
     */
    public function assignPermission($roleId, $permissionId): bool
    {
        $role = $this->find($roleId);
        if (!$role) {
            return false;
        }

        // التحقق من عدم وجود الصلاحية مسبقاً
        if (!$role->permissions()->where('permission_id', $permissionId)->exists()) {
            $role->permissions()->attach($permissionId);
        }

        return true;
    }

    /**
     * إزالة صلاحية من دور
     */
    public function removePermission($roleId, $permissionId): bool
    {
        $role = $this->find($roleId);
        if (!$role) {
            return false;
        }

        $role->permissions()->detach($permissionId);
        return true;
    }

    /**
     * الحصول على جميع صلاحيات الدور
     */
    public function getPermissions($roleId): Collection
    {
        $role = $this->find($roleId);
        return $role ? $role->permissions : collect();
    }

    /**
     * مزامنة صلاحيات الدور (استبدال جميع الصلاحيات)
     */
    public function syncPermissions($roleId, array $permissionIds): bool
    {
        $role = $this->find($roleId);
        if (!$role) {
            return false;
        }

        $role->permissions()->sync($permissionIds);
        return true;
    }

    /**
     * الحصول على الأدوار غير النظامية فقط
     */
    public function getNonSystemRoles(): Collection
    {
        return $this->repository->all()->where('is_system', false);
    }

    /**
     * التحقق من امتلاك الدور لصلاحية معينة
     */
    public function hasPermission($roleId, string $permissionName): bool
    {
        $role = $this->find($roleId);
        if (!$role) {
            return false;
        }

        return $role->permissions()->where('name', $permissionName)->exists();
    }
}
