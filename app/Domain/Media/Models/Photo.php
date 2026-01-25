<?php

namespace App\Domain\Media\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Photo extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'photo_id';

    protected $fillable = [
        'album_id', 'original_name', 'stored_name', 'file_path', 'file_size',
        'mime_type', 'width', 'height', 'caption', 'tags',
        'is_hidden', 'is_archived', 'view_count', 'download_count', 'uploaded_at'
    ];

    protected $casts = [
        'tags' => 'array',
        'is_hidden' => 'boolean',
        'is_archived' => 'boolean',
        'uploaded_at' => 'datetime',
    ];

    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id');
    }
}

