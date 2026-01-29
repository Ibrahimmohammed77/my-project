<?php

namespace App\Observers;

use App\Models\Photo;
use App\Models\ActivityLog;

class PhotoObserver
{
    public function created(Photo $photo): void
    {
        ActivityLog::logActivity(
            $photo->album->user_id ?? null,
            'upload',
            'photo',
            $photo->photo_id,
            ['filename' => $photo->original_name]
        );
    }

    public function deleted(Photo $photo): void
    {
        ActivityLog::logActivity(
            $photo->album->user_id ?? null,
            'delete',
            'photo',
            $photo->photo_id,
            ['filename' => $photo->original_name]
        );
    }
}
