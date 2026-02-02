<?php

namespace App\UseCases\Studio;

use App\Models\StorageLibrary;
use App\Models\Album;
use App\Models\Studio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateStorageLibraryWithHiddenAlbumUseCase
{
    /**
     * إنشاء storage library مع ألبوم مخفي تلقائياً
     *
     * @param Studio $studio
     * @param array $data
     * @return StorageLibrary
     * @throws \Exception
     */
    public function execute(Studio $studio, array $data): StorageLibrary
    {
        DB::beginTransaction();
        
        try {
            // إنشاء storage library
            $library = StorageLibrary::create([
                'studio_id' => $studio->studio_id,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'storage_limit' => $data['storage_limit'] ?? 0,
            ]);

            // إنشاء ألبوم مخفي تلقائياً
            $hiddenAlbum = Album::create([
                'owner_type' => Studio::class,
                'owner_id' => $studio->studio_id,
                'storage_library_id' => $library->storage_library_id,
                'name' => "Hidden - {$data['name']}",
                'description' => "Automatically created hidden album for storage library: {$data['name']}",
                'is_visible' => false,
                'is_hidden' => true,
                'is_default' => false,
            ]);

            // ربط الألبوم المخفي بالمكتبة
            $library->update(['hidden_album_id' => $hiddenAlbum->album_id]);

            DB::commit();

            Log::info('Storage library created with hidden album', [
                'library_id' => $library->storage_library_id,
                'studio_id' => $studio->studio_id,
                'hidden_album_id' => $hiddenAlbum->album_id,
            ]);

            return $library->load('hiddenAlbum');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to create storage library with hidden album', [
                'studio_id' => $studio->studio_id,
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }
}
