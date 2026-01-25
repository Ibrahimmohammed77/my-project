<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $primaryKey = 'card_id';

    protected $fillable = [
        'card_uuid', 'card_number', 'card_group_id', 
        'owner_type', 'owner_id', 'holder_id',
        'card_type_id', 'card_status_id', 
        'activation_date', 'expiry_date',
        'usage_count', 'last_used', 'metadata', 'notes'
    ];

    protected $casts = [
        'activation_date' => 'datetime',
        'expiry_date' => 'date',
        'last_used' => 'datetime',
        'metadata' => 'array',
    ];

    public function group()
    {
        return $this->belongsTo(CardGroup::class, 'card_group_id');
    }

    public function owner()
    {
        return $this->morphTo();
    }

    public function holder()
    {
        return $this->belongsTo(Account::class, 'holder_id');
    }

    public function type()
    {
        return $this->belongsTo(LookupValue::class, 'card_type_id');
    }

    public function status()
    {
        return $this->belongsTo(LookupValue::class, 'card_status_id');
    }

    public function albums()
    {
        return $this->belongsToMany(Album::class, 'card_albums', 'card_id', 'album_id')
                    ->withPivot('created_at');
    }
}
