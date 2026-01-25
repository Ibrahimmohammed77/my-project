<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Album extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'album_id';

    protected $fillable = [
        'owner_type', 'owner_id', 'name', 'description', 
        'is_default', 'is_visible', 'view_count', 'settings'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_visible' => 'boolean',
        'settings' => 'array',
    ];

    public function owner()
    {
        return $this->morphTo();
    }

    public function photos()
    {
        return $this->hasMany(Photo::class, 'album_id');
    }
}
