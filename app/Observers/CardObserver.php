<?php

namespace App\Observers;

use App\Models\Card;
use App\Models\ActivityLog;

class CardObserver
{
    public function created(Card $card): void
    {
        ActivityLog::logActivity(
            null,
            'create',
            'card',
            $card->id,
            ['name' => $card->name]
        );
    }

    public function updated(Card $card): void
    {
        $changes = $card->getChanges();
        
        if (!empty($changes)) {
            ActivityLog::logActivity(
                null,
                'update',
                'card',
                $card->id,
                ['changes' => $changes]
            );
        }
    }
}
