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
        return $this->repository->delete($id);
    }
    
    public function find($id): ?Model
    {
        return $this->repository->find($id);
    }
}
