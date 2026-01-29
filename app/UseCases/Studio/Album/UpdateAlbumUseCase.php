<?php

namespace App\UseCases\Studio\Album;

use App\Models\Album;
use App\Models\Studio;
use Exception;

class UpdateAlbumUseCase
{
    /**
     * Execute the use case.
     */
    public function execute(Studio $studio, int $albumId, array $data): Album
    {
        $album = $studio->albums()->findOrFail($albumId);

        $album->update([
            'name' => $data['name'] ?? $album->name,
            'description' => $data['description'] ?? $album->description,
            'storage_library_id' => $data['storage_library_id'] ?? $album->storage_library_id,
            'is_visible' => $data['is_visible'] ?? $album->is_visible,
            'settings' => array_merge($album->settings ?? [], $data['settings'] ?? []),
        ]);

        return $album;
    }
}
