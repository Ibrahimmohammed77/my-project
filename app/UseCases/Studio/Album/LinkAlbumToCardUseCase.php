<?php

namespace App\UseCases\Studio\Album;

use App\Models\Album;
use App\Models\Card;
use App\Models\Studio;
use Exception;

class LinkAlbumToCardUseCase
{
    /**
     * Execute the use case.
     */
    public function execute(Studio $studio, int $albumId, array $cardIds): void
    {
        $album = $studio->albums()->findOrFail($albumId);

        // Verify all cards belong to this studio
        $count = $studio->cards()->whereIn('card_id', $cardIds)->count();
        if ($count !== count($cardIds)) {
            throw new Exception('بعض الكروت المحددة غير تابعة لهذا الاستوديو');
        }
        
        $album->cards()->sync($cardIds);
    }
}
