<?php

namespace App\UseCases\Admin\Role;

use App\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ListPermissionsUseCase
{
    protected $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function execute(): Collection
    {
        return $this->roleRepository->listPermissions();
    }
}
