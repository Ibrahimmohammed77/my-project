<?php

namespace App\Domain\Identity\Services;

use App\Domain\Identity\Repositories\Contracts\PermissionRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class PermissionService
{
    protected $repository;

    public function __construct(PermissionRepositoryInterface $repository)
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
     * البحث عن صلاحية بناءً على resource_type و action
     */
    public function findByResourceAndAction(string $resourceType, string $action): ?Model
    {
        return $this->repository->findWhere([
            'resource_type' => $resourceType,
            'action' => $action
        ])->first();
    }

    /**
     * الحصول على جميع الصلاحيات مجمعة حسب resource_type
     */
    public function getGroupedByResource(): Collection
    {
        return $this->repository->all()->groupBy('resource_type');
    }
}
