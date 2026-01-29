<?php

namespace App\Observers;

use App\Models\Card;
use App\Models\ActivityLog;

class CardObserver
{
    public function created(Card $card): void
    {
        ActivityLog::log(
            auth()->id(),
            'create',
            'card',
            $card->card_id,
            ['card_number' => $card->card_number]
        );
    }

    public function updated(Card $card): void
    {
        $changes = $card->getChanges();
        
        if (!empty($changes)) {
            ActivityLog::log(
                auth()->id(),
                'update',
                'card',
                $card->card_id,
                ['changes' => $changes]
            );
        }
    }
}
