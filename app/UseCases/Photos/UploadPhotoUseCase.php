<?php

namespace App\UseCases\Photos;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadPhotoUseCase
{
    public function execute(Album $album, UploadedFile $file, array $data = []): Photo
    {
        // 1. Generate path
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::uuid() . '.' . $extension;
        
        // Structure: public/photos/{album_id}/{uuid}.jpg
        $path = "public/photos/{$album->album_id}";
        
        // 2. Store file
        $storedPath = $file->storeAs($path, $fileName);
        
        // 3. Get Dimensions (if image)
        $width = 0;
        $height = 0;
        
        if (str_starts_with($file->getMimeType(), 'image/')) {
            [$width, $height] = getimagesize($file->getPathname());
        }

        // 4. Create Photo Record
        $photo = Photo::create([
            'album_id' => $album->album_id,
            'original_name' => $file->getClientOriginalName(),
            'stored_name' => $fileName,
            'file_path' => $storedPath,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'width' => $width,
            'height' => $height,
            'caption' => $data['caption'] ?? null,
            'tags' => $data['tags'] ?? null,
            'is_hidden' => $data['is_hidden'] ?? false,
            'review_status' => Photo::STATUS_APPROVED, // Auto-approve for now, or make configurable
        ]);

        return $photo;
    }
}
