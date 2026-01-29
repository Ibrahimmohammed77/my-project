<?php

namespace App\Repositories;

use App\Models\Card;
use App\Models\CardGroup;
use App\Repositories\Contracts\CardRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CardRepository implements CardRepositoryInterface
{
    public function listGroups(int $perPage = 15): LengthAwarePaginator
    {
        return CardGroup::withCount('cards')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findGroup(int $id): ?CardGroup
    {
        return CardGroup::find($id);
    }

    public function listCardsByGroup(CardGroup $group, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $group->cards()->with(['type', 'status', 'holder']);

        if (!empty($filters['search'])) {
            $query->where('card_number', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('card_uuid', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['status_id'])) {
            $query->where('card_status_id', $filters['status_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function storeGroup(array $data): CardGroup
    {
        return DB::transaction(function () use ($data) {
            return CardGroup::create($data);
        });
    }

    public function updateGroup(CardGroup $group, array $data): CardGroup
    {
        return DB::transaction(function () use ($group, $data) {
            $group->update($data);
            return $group->refresh();
        });
    }

    public function deleteGroup(CardGroup $group): bool
    {
        return $group->delete();
    }

    public function storeCard(array $data): Card
    {
        return DB::transaction(function () use ($data) {
            // Ensure card_uuid is generated if not present (Model boot handles it usually, but good to be safe)
            return Card::create($data);
        });
    }

    public function updateCard(Card $card, array $data): Card
    {
        return DB::transaction(function () use ($card, $data) {
            $card->update($data);
            return $card->refresh();
        });
    }

    public function deleteCard(Card $card): bool
    {
        return $card->delete();
    }

    public function findCard(int $id): ?Card
    {
        return Card::find($id);
    }
}
