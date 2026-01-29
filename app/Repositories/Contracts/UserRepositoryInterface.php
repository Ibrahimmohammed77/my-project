<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create(array $data): User;
    public function find(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function findByPhone(string $phone): ?User;
    public function findByUsername(string $username): ?User;
    public function findByLogin(string $login): ?User;
    public function update(User $user, array $data): bool;
    public function delete(User $user): bool;
    public function assignRole(User $user, string $role): void;
    public function hasRole(User $user, string $role): bool;
    public function getCustomerTypeId(): int;
    public function getDefaultStatusId(): int;
}
