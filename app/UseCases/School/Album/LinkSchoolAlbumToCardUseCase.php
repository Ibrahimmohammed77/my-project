<?php

namespace App\UseCases\School\Album;

use App\Models\School;
use Exception;
use Illuminate\Support\Facades\DB;

class LinkSchoolAlbumToCardUseCase
{
    /**
     * Execute the use case.
     */
    public function execute(School $school, int $albumId, array $cardIds): void
    {
        $album = $school->albums()->findOrFail($albumId);

        // Verify that all cards are owned by this school
        $cardsCount = $school->cards()->whereIn('card_id', $cardIds)->count();
        if ($cardsCount !== count($cardIds)) {
            throw new Exception('بعض الكروت المحددة لا تنتمي لهذه المدرسة');
        }

        DB::transaction(function () use ($album, $cardIds) {
            $album->cards()->sync($cardIds);
        });
    }
}
