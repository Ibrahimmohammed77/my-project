<?php

namespace App\Repositories\Contracts;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

interface RoleRepositoryInterface
{
    public function listRoles(): Collection;
    public function listPermissions(): Collection;
    public function storeRole(array $data): Role;
    public function updateRole(Role $role, array $data): Role;
    public function deleteRole(Role $role): bool;
    public function syncPermissions(Role $role, array $permissionIds): Role;
    public function findRole(int $id): ?Role;
}
