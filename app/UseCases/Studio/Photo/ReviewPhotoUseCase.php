<?php

namespace App\UseCases\Studio\Photo;

use App\Models\Photo;
use App\Models\Studio;
use Exception;

class ReviewPhotoUseCase
{
    /**
     * Execute the use case.
     */
    public function execute(Studio $studio, int $photoId, string $status, ?string $reason = null): Photo
    {
        $photo = Photo::whereHas('album.storageLibrary', function($q) use ($studio) {
            $q->where('studio_id', $studio->studio_id);
        })->findOrFail($photoId);

        if (!in_array($status, [Photo::STATUS_APPROVED, Photo::STATUS_REJECTED])) {
            throw new Exception('حالة مراجعة غير صالحة');
        }

        $photo->update([
            'review_status' => $status,
            'rejection_reason' => $status === Photo::STATUS_REJECTED ? $reason : null,
        ]);

        return $photo;
    }
}
