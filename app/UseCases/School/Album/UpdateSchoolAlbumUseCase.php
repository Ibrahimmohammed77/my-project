<?php

namespace App\UseCases\School\Album;

use App\Models\Album;
use App\Models\School;
use Exception;

class UpdateSchoolAlbumUseCase
{
    /**
     * Execute the use case.
     */
    public function execute(School $school, int $albumId, array $data): Album
    {
        $album = $school->albums()->findOrFail($albumId);

        $album->update([
            'name' => $data['name'] ?? $album->name,
            'description' => $data['description'] ?? $album->description,
            'is_visible' => $data['is_visible'] ?? $album->is_visible,
            'settings' => array_merge($album->settings ?? [], $data['settings'] ?? []),
        ]);

        return $album;
    }
}
