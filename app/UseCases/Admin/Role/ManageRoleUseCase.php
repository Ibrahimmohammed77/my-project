<?php

namespace App\UseCases\Admin\Role;

use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class ManageRoleUseCase
{
    protected $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function listRoles(): Collection
    {
        return $this->roleRepository->listRoles();
    }

    public function createRole(array $data): Role
    {
        return $this->roleRepository->storeRole($data);
    }

    public function updateRole(Role $role, array $data): Role
    {
        return $this->roleRepository->updateRole($role, $data);
    }

    public function deleteRole(Role $role): bool
    {
        if ($role->is_system) {
            throw new \Exception('System roles cannot be deleted.');
        }
        return $this->roleRepository->deleteRole($role);
    }

    public function getAvailablePermissions(): Collection
    {
        return $this->roleRepository->listPermissions();
    }
}
