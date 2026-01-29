<?php

namespace App\Domain\Identity\Repositories\Eloquent;

use App\Domain\Shared\Repositories\Eloquent\BaseRepository;
use App\Domain\Identity\Repositories\Contracts\PermissionRepositoryInterface;
use App\Domain\Identity\Models\Permission;

class PermissionRepository extends BaseRepository implements PermissionRepositoryInterface
{
    public function __construct(Permission $permission)
    {
        parent::__construct($permission);
    }

    public function findByResourceType(string $resourceType)
    {
        return $this->model->where('resource_type', $resourceType)->get();
    }

    public function findByName(string $name)
    {
        return $this->model->where('name', $name)->first();
    }

    public function search(string $searchTerm)
    {
        return $this->model
            ->where('name', 'LIKE', "%{$searchTerm}%")
            ->orWhere('resource_type', 'LIKE', "%{$searchTerm}%")
            ->orWhere('action', 'LIKE', "%{$searchTerm}%")
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
