<?php

namespace App\UseCases\Studio;

use App\Models\Card;
use App\Models\StorageLibrary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignCardToLibraryUseCase
{
    /**
     * ربط الكرت بمكتبة التخزين والألبوم المخفي
     *
     * @param Card $card
     * @param StorageLibrary $library
     * @return Card
     * @throws \Exception
     */
    public function execute(Card $card, StorageLibrary $library): Card
    {
        DB::beginTransaction();
        
        try {
            // التحقق من وجود ألبوم مخفي
            if (!$library->hasHiddenAlbum()) {
                throw new \Exception('Storage library does not have a hidden album');
            }

            // ربط الكرت بمكتبة التخزين
            $card->update(['storage_library_id' => $library->storage_library_id]);

            // ربط الكرت بالألبوم المخفي
            $card->attachAlbum($library->hidden_album_id);

            DB::commit();

            Log::info('Card assigned to storage library', [
                'card_id' => $card->card_id,
                'library_id' => $library->storage_library_id,
                'hidden_album_id' => $library->hidden_album_id,
            ]);

            return $card->fresh()->loadCardRelations();
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to assign card to storage library', [
                'card_id' => $card->card_id,
                'library_id' => $library->storage_library_id,
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }
}
