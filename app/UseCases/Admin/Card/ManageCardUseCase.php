<?php

namespace App\UseCases\Admin\Card;

use App\Repositories\Contracts\CardRepositoryInterface;
use App\Models\Card;
use App\Models\CardGroup;
use Illuminate\Pagination\LengthAwarePaginator;

class ManageCardUseCase
{
    protected $cardRepository;

    public function __construct(CardRepositoryInterface $cardRepository)
    {
        $this->cardRepository = $cardRepository;
    }

    public function listCardsByGroup(CardGroup $group, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->cardRepository->listCardsByGroup($group, $filters, $perPage);
    }

    public function createCard(array $data): Card
    {
        return $this->cardRepository->storeCard($data);
    }

    public function updateCard(Card $card, array $data): Card
    {
        return $this->cardRepository->updateCard($card, $data);
    }

    public function deleteCard(Card $card): bool
    {
        return $this->cardRepository->deleteCard($card);
    }
}
