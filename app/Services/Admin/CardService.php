<?php

namespace App\Services\Admin;

use App\Models\{Card, CardGroup};
use App\Repositories\Contracts\CardRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CardService
{
    public function __construct(
        protected CardRepositoryInterface $cardRepository
    ) {}

    // ==================== CARD GROUP METHODS ====================

    public function listGroups(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = CardGroup::query();

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        return $query->withCount('cards')
                    ->ordered()
                    ->paginate($perPage);
    }

    public function createGroup(array $data): CardGroup
    {
        return DB::transaction(function () use ($data) {
            return CardGroup::create($data);
        });
    }

    public function updateGroup(CardGroup $group, array $data): CardGroup
    {
        return DB::transaction(function () use ($group, $data) {
            $group->update($data);
            return $group->fresh();
        });
    }

    public function deleteGroup(CardGroup $group): bool
    {
        return DB::transaction(function () use ($group) {
            // Delete all cards in the group first
            $group->cards()->delete();

            // Then delete the group
            return $group->delete();
        });
    }

    // ==================== CARD METHODS ====================

    public function listCards(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Card::query()->withCardRelations();

        // Apply filters
        foreach ($filters as $key => $value) {
            if (empty($value)) continue;

            switch ($key) {
                case 'search':
                    $query->search($value);
                    break;

                case 'group_id':
                    $query->byGroup($value);
                    break;

                case 'type_id':
                case 'card_type_id':
                    $query->byType($value);
                    break;

                case 'status_id':
                case 'card_status_id':
                    $query->byStatus($value);
                    break;

                case 'holder_id':
                    $query->where('holder_id', $value);
                    break;
            }
        }

        return $query->latest('created_at')->paginate($perPage);
    }

    public function listCardsByGroup(CardGroup $group, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $filters['group_id'] = $group->group_id;
        return $this->listCards($filters, $perPage);
    }

    public function createCard(array $data): Card
    {
        return DB::transaction(function () use ($data) {
            // Generate card number if not provided
            if (empty($data['card_number'])) {
                $data['card_number'] = Card::generateCardNumber();
            }

            $card = Card::create($data);

            // Increment used cards in group
            if ($card->group) {
                $card->group->incrementUsedCards();
            }

            return $card;
        });
    }

    public function updateCard(Card $card, array $data): Card
    {
        return DB::transaction(function () use ($card, $data) {
            $oldGroupId = $card->card_group_id;
            $newGroupId = $data['card_group_id'] ?? $oldGroupId;

            $card->update($data);

            // Handle group changes
            if ($oldGroupId != $newGroupId) {
                // Decrement from old group
                $oldGroup = CardGroup::find($oldGroupId);
                if ($oldGroup) {
                    $oldGroup->decrementUsedCards();
                }

                // Increment in new group
                $newGroup = CardGroup::find($newGroupId);
                if ($newGroup) {
                    $newGroup->incrementUsedCards();
                }
            }

            return $card->fresh()->loadCardRelations();
        });
    }

    public function deleteCard(Card $card): bool
    {
        return DB::transaction(function () use ($card) {
            // Decrement used cards in group
            if ($card->group) {
                $card->group->decrementUsedCards();
            }

            return $card->delete();
        });
    }

    public function activateCard(Card $card): bool
    {
        return $card->activate();
    }

    public function deactivateCard(Card $card): bool
    {
        return $card->deactivate();
    }

    public function linkCardToAlbums(Card $card, array $albumIds): void
    {
        DB::transaction(function () use ($card, $albumIds) {
            $card->syncAlbums($albumIds);
        });
    }

    // ==================== STATISTICS ====================

    public function getStatistics(): array
    {
        return [
            'total_groups' => CardGroup::count(),
            'total_cards' => Card::count(),
            'active_cards' => Card::active()->count(),
            'inactive_cards' => Card::inactive()->count(),
            'expired_cards' => Card::expired()->count(),
            'cards_without_holder' => Card::whereNull('holder_id')->count(),
        ];
    }

    public function getGroupStatistics(int $groupId): array
    {
        $group = CardGroup::findOrFail($groupId);

        return [
            'group_info' => [
                'name' => $group->name,
                'available_cards' => $group->available_cards,
                'total_capacity' => $group->sub_card_available,
                'used_capacity' => $group->sub_card_used,
                'usage_percentage' => $group->usage_percentage,
            ],
            'cards_stats' => $group->getStats(),
        ];
    }
}
