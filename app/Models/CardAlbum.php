<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardAlbum extends Model
{
    use HasFactory;

    protected $table = 'card_albums';
    protected $primaryKey = 'card_album_id';

    /**
     * علاقة البطاقة
     */
    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id', 'card_id');
    }

    /**
     * علاقة الألبوم
     */
    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id', 'album_id');
    }

    /**
     * التحقق مما إذا كانت البطاقة يمكنها الوصول إلى الألبوم
     */
    public static function canAccess($cardId, $albumId)
    {
        return self::where('card_id', $cardId)
                    ->where('album_id', $albumId)
                    ->exists();
    }
}
