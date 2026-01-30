<?php

namespace App\Repositories\Contracts;

use App\Models\Subscription;
use Illuminate\Pagination\LengthAwarePaginator;

interface SubscriptionRepositoryInterface
{
    /**
     * List subscriptions with filters.
     */
    public function listByAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a subscription by ID.
     */
    public function find(int $id): ?Subscription;

    /**
     * Create or update a subscription.
     */
    public function store(array $data): Subscription;

    /**
     * Delete a subscription.
     */
    public function delete(Subscription $subscription): bool;
}
