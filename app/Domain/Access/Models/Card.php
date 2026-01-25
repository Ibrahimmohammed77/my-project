<?php

namespace App\Domain\Access\Models;

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
        return $this->belongsTo(\App\Domain\Identity\Models\Account::class, 'holder_id');
    }

    public function type()
    {
        return $this->belongsTo(\App\Domain\Shared\Models\LookupValue::class, 'card_type_id');
    }

    public function status()
    {
        return $this->belongsTo(\App\Domain\Shared\Models\LookupValue::class, 'card_status_id');
    }

    public function albums()
    {
        return $this->belongsToMany(\App\Domain\Media\Models\Album::class, 'card_albums', 'card_id', 'album_id')
                    ->withPivot('created_at');
    }

    public function scopeActive($query)
    {
        return $query->whereHas('status', function ($q) {
            $q->where('code', 'ACTIVE');
        })->where(function($q) {
             $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now());
        });
    }
}


