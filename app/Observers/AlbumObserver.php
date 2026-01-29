<?php

namespace App\Observers;

use App\Models\Album;
use App\Models\ActivityLog;

class AlbumObserver
{
    public function created(Album $album): void
    {
        ActivityLog::logActivity(
            $album->user_id,
            'create',
            'album',
            $album->id,
            ['name' => $album->name]
        );
    }

    public function updated(Album $album): void
    {
        $changes = $album->getChanges();
        
        if (!empty($changes)) {
            ActivityLog::logActivity(
                $album->user_id,
                'update',
                'album',
                $album->id,
                ['changes' => $changes]
            );
        }
    }

    public function deleted(Album $album): void
    {
        ActivityLog::logActivity(
            $album->user_id,
            'delete',
            'album',
            $album->id,
            ['name' => $album->name]
        );
    }
}
