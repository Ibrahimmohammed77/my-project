<?php

namespace App\UseCases\Admin\Card;

use App\Repositories\Contracts\CardRepositoryInterface;
use App\Models\CardGroup;
use Illuminate\Pagination\LengthAwarePaginator;

class ManageCardGroupUseCase
{
    protected $cardRepository;

    public function __construct(CardRepositoryInterface $cardRepository)
    {
        $this->cardRepository = $cardRepository;
    }

    public function listGroups(int $perPage = 15): LengthAwarePaginator
    {
        return $this->cardRepository->listGroups($perPage);
    }

    public function createGroup(array $data): CardGroup
    {
        return $this->cardRepository->storeGroup($data);
    }

    public function updateGroup(CardGroup $group, array $data): CardGroup
    {
        return $this->cardRepository->updateGroup($group, $data);
    }

    public function deleteGroup(CardGroup $group): bool
    {
        return $this->cardRepository->deleteGroup($group);
    }
}
