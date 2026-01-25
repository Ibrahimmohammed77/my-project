<?php

namespace App\Domain\Communications\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $primaryKey = 'notification_id';
    public $timestamps = false; // Custom timestamps handled manually or via defaults, schema has sent_at etc.

    protected $fillable = [
        'account_id', 'title', 'message', 'notification_type_id',
        'is_read', 'metadata', 'sent_at', 'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'metadata' => 'array',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function account()
    {
        return $this->belongsTo(\App\Domain\Identity\Models\Account::class, 'account_id');
    }

    public function type()
    {
        return $this->belongsTo(\App\Domain\Shared\Models\LookupValue::class, 'notification_type_id');
    }
}


