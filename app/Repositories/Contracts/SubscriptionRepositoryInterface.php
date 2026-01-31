<?php

namespace App\Repositories\Contracts;

use App\Models\Subscription;
use Illuminate\Pagination\LengthAwarePaginator;

interface SubscriptionRepositoryInterface
{
    public function listByAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function find(int $id): ?Subscription;
    public function store(array $data): Subscription;
    public function update(Subscription $subscription, array $data): Subscription;
    public function delete(Subscription $subscription): bool;
    public function findByUser(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findActiveByUser(int $userId): ?Subscription;
}