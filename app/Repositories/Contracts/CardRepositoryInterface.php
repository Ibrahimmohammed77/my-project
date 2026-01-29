<?php

namespace App\Repositories\Contracts;

use App\Models\Card;
use App\Models\CardGroup;
use Illuminate\Pagination\LengthAwarePaginator;

interface CardRepositoryInterface
{
    public function listGroups(int $perPage = 15): LengthAwarePaginator;
    public function findGroup(int $id): ?CardGroup;
    public function listCardsByGroup(CardGroup $group, array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function storeGroup(array $data): CardGroup;
    public function updateGroup(CardGroup $group, array $data): CardGroup;
    public function deleteGroup(CardGroup $group): bool;
    
    public function storeCard(array $data): Card;
    public function updateCard(Card $card, array $data): Card;
    public function deleteCard(Card $card): bool;
    public function findCard(int $id): ?Card;
}
