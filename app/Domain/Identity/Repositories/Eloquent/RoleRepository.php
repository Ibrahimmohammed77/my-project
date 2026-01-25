<?php

namespace App\Domain\Identity\Repositories\Eloquent;

use App\Domain\Shared\Repositories\Eloquent\BaseRepository;
use App\Domain\Identity\Repositories\Contracts\RoleRepositoryInterface;
use App\Domain\Identity\Models\Role;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(Role $role)
    {
        parent::__construct($role);
    }
}
