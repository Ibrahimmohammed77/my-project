<?php

namespace App\UseCases\Studio\Card;

use App\Models\Card;
use App\Models\Studio;
use Exception;

class LinkCardToAlbumUseCase
{
    /**
     * Execute the use case.
     */
    public function execute(Studio $studio, int $cardId, array $albumIds): void
    {
        \Illuminate\Support\Facades\Log::info("Executing LinkCardToAlbumUseCase for card {$cardId}");
        // Find the card and ensure it belongs to this studio
        $card = $studio->cards()->findOrFail($cardId);

        // Verify all albums belong to this studio
        $count = $studio->albums()->whereIn('album_id', $albumIds)->count();
        if ($count !== count($albumIds)) {
            throw new Exception('بعض الألبومات المحددة غير تابعة لهذا الاستوديو');
        }
        
        // Sync albums to the card
        $card->albums()->sync($albumIds);
    }
}
