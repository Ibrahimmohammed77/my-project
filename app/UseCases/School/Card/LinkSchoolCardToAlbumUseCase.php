<?php

namespace App\UseCases\School\Card;

use App\Models\School;
use Exception;
use Illuminate\Support\Facades\DB;

class LinkSchoolCardToAlbumUseCase
{
    /**
     * Execute the use case.
     */
    public function execute(School $school, int $cardId, array $albumIds): void
    {
        $card = $school->cards()->findOrFail($cardId);

        // Verify that all albums are owned by this school
        $albumsCount = $school->albums()->whereIn('album_id', $albumIds)->count();
        if ($albumsCount !== count($albumIds)) {
            throw new Exception('بعض الألبومات المحددة لا تنتمي لهذه المدرسة');
        }

        DB::transaction(function () use ($card, $albumIds) {
            $card->albums()->sync($albumIds);
        });
    }
}
