<?php

namespace App\Domain\Core\Services;

use App\Domain\Core\Repositories\Contracts\SchoolRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class SchoolService
{
    protected $repository;

    public function __construct(SchoolRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }


    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    public function getAllActive(): Collection
    {
        return $this->repository->findActive();
    }

    public function getAllByStatus(string $statusCode): Collection
    {
        return $this->repository->findByStatus($statusCode);
    }

    public function getAllByType(string $typeCode): Collection
    {
        return $this->repository->findByType($typeCode);
    }

    public function getByAccount(int $accountId): Collection
    {
        return $this->repository->findByAccount($accountId);
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

    public function findWithRelations($id, array $relations = []): ?Model
    {
        return $this->repository->findWithRelations($id, $relations);
    }

    public function search(string $searchTerm): Collection
    {
        return $this->repository->search($searchTerm);
    }

    public function activate($id): bool
    {
        return $this->repository->updateStatus($id, 'ACTIVE');
    }

    public function deactivate($id): bool
    {
        return $this->repository->updateStatus($id, 'INACTIVE');
    }

    public function paginate(int $perPage = 15)
    {
        return $this->repository->paginate($perPage);
    }
}
