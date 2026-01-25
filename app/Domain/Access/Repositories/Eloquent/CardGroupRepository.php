<?php

namespace App\Domain\Access\Repositories\Eloquent;

use App\Domain\Shared\Repositories\Eloquent\BaseRepository;
use App\Domain\Access\Repositories\Contracts\CardGroupRepositoryInterface;
use App\Domain\Access\Models\CardGroup;

class CardGroupRepository extends BaseRepository implements CardGroupRepositoryInterface
{
    public function __construct(CardGroup $cardGroup)
    {
        parent::__construct($cardGroup);
    }
}
