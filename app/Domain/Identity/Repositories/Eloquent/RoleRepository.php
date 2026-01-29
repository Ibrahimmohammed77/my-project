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

    public function findSystemRoles()
    {
        return $this->model->where('is_system', true)->get();
    }

    public function findNonSystemRoles()
    {
        return $this->model->where('is_system', false)->get();
    }

    public function findByName(string $name)
    {
        return $this->model->where('name', $name)->first();
    }

    public function search(string $searchTerm)
    {
        return $this->model
            ->where('name', 'LIKE', "%{$searchTerm}%")
            ->orWhere('description', 'LIKE', "%{$searchTerm}%")
            ->get();
    }

    public function findWithRelations(int $id, array $relations = [])
    {
        $query = $this->model->newQuery();
        
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        return $query->find($id);
    }
}
