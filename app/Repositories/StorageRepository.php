<?php

namespace App\Repositories;

use App\Models\StorageAccount;
use App\Repositories\Contracts\StorageRepositoryInterface;
use Illuminate\Support\Collection;

class StorageRepository implements StorageRepositoryInterface
{
    protected $model;

    public function __construct(StorageAccount $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(int $id): ?StorageAccount
    {
        return $this->model->find($id);
    }

    public function create(array $data): StorageAccount
    {
        return $this->model->create($data);
    }

    public function update(StorageAccount $account, array $data): bool
    {
        return $account->update($data);
    }

    public function delete(StorageAccount $account): bool
    {
        return $account->delete();
    }

    public function getForUser(int $userId): ?StorageAccount
    {
        return $this->model->where('user_id', $userId)->first();
    }
}
