<?php

namespace App\Repositories\Contracts;

use App\Models\StorageAccount;
use Illuminate\Support\Collection;

interface StorageRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?StorageAccount;
    public function create(array $data): StorageAccount;
    public function update(StorageAccount $account, array $data): bool;
    public function delete(StorageAccount $account): bool;
    public function getForUser(int $userId): ?StorageAccount;
}
