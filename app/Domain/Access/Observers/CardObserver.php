<?php

namespace App\Domain\Access\Observers;

use App\Domain\Access\Models\Card;
use Illuminate\Support\Str;

class CardObserver
{
    /**
     * Handle the Card "creating" event.
     */
    public function creating(Card $card): void
    {
        if (empty($card->card_uuid)) {
            $card->card_uuid = (string) Str::uuid();
        }
    }
}


