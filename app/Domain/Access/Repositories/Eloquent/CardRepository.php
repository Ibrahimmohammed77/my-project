<?php

namespace App\Domain\Access\Repositories\Eloquent;

use App\Domain\Shared\Repositories\Eloquent\BaseRepository;
use App\Domain\Access\Repositories\Contracts\CardRepositoryInterface;
use App\Domain\Access\Models\Card;

class CardRepository extends BaseRepository implements CardRepositoryInterface
{
    public function __construct(Card $card)
    {
        parent::__construct($card);
    }
}
