<?php

namespace App\Domain\Identity\Services;

use App\Domain\Identity\Repositories\Contracts\AccountRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class AccountService
{
    protected $repository;

    public function __construct(AccountRepositoryInterface $repository)
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
        return $this->repository->delete($id);
    }
    
    public function find($id): ?Model
    {
        return $this->repository->find($id);
    }

    /**
     * تعيين دور لحساب
     */
    public function assignRole($accountId, $roleId): bool
    {
        $account = $this->find($accountId);
        if (!$account) {
            return false;
        }

        // التحقق من عدم وجود الدور مسبقاً
        if (!$account->roles()->where('role_id', $roleId)->exists()) {
            $account->roles()->attach($roleId);
        }

        return true;
    }

    /**
     * إزالة دور من حساب
     */
    public function removeRole($accountId, $roleId): bool
    {
        $account = $this->find($accountId);
        if (!$account) {
            return false;
        }

        $account->roles()->detach($roleId);
        return true;
    }

    /**
     * الحصول على جميع أدوار الحساب
     */
    public function getRoles($accountId): Collection
    {
        $account = $this->find($accountId);
        return $account ? $account->roles : collect();
    }

    /**
     * التحقق من امتلاك الحساب لدور معين
     */
    public function hasRole($accountId, string $roleName): bool
    {
        $account = $this->find($accountId);
        if (!$account) {
            return false;
        }

        return $account->roles()->where('name', $roleName)->exists();
    }

    /**
     * التحقق من امتلاك الحساب لصلاحية معينة
     */
    public function hasPermission($accountId, string $permissionName): bool
    {
        $account = $this->find($accountId);
        if (!$account) {
            return false;
        }

        // البحث في جميع أدوار الحساب عن الصلاحية
        return $account->roles()
            ->whereHas('permissions', function ($query) use ($permissionName) {
                $query->where('name', $permissionName);
            })
            ->exists();
    }

    /**
     * الحصول على جميع صلاحيات الحساب من خلال أدواره
     */
    public function getAllPermissions($accountId): Collection
    {
        $account = $this->find($accountId);
        if (!$account) {
            return collect();
        }

        $permissions = collect();
        foreach ($account->roles as $role) {
            $permissions = $permissions->merge($role->permissions);
        }

        return $permissions->unique('permission_id');
    }

    /**
     * البحث عن حسابات بحالة معينة
     */
    public function getByStatus(string $statusCode): Collection
    {
        return $this->repository->findWhere([])
            ->filter(function ($account) use ($statusCode) {
                return $account->status && $account->status->code === $statusCode;
            });
    }

    /**
     * الحصول على الحسابات النشطة فقط
     */
    public function getActive(): Collection
    {
        return $this->getByStatus('ACTIVE');
    }
}
