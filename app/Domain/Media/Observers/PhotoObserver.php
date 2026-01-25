<?php

namespace App\Domain\Media\Observers;

use App\Domain\Media\Models\Photo;
use Illuminate\Support\Facades\Storage;

class PhotoObserver
{
    /**
     * Handle the Photo "deleted" event.
     */
    public function deleted(Photo $photo): void
    {
        if ($photo->file_path && Storage::exists($photo->file_path)) {
            Storage::delete($photo->file_path);
        }
    }
}


