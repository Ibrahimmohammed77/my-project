<?php

namespace App\Observers;

use App\Models\Album;
use App\Models\ActivityLog;

class AlbumObserver
{
    public function created(Album $album): void
    {
        ActivityLog::logActivity(
            $album->owner_id,
            'create',
            'album',
            $album->album_id,
            ['name' => $album->name]
        );
    }

    public function updated(Album $album): void
    {
        $changes = $album->getChanges();
        
        if (!empty($changes)) {
            ActivityLog::logActivity(
                $album->owner_id,
                'update',
                'album',
                $album->album_id,
                ['changes' => $changes]
            );
        }
    }

    public function deleted(Album $album): void
    {
        ActivityLog::logActivity(
            $album->owner_id,
            'delete',
            'album',
            $album->album_id,
            ['name' => $album->name]
        );
    }
}
