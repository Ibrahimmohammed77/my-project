<?php

namespace App\Domain\Shared\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $primaryKey = 'log_id';
    public $timestamps = false; // Only created_at in schema

    protected $fillable = [
        'account_id', 'action', 'resource_type', 'resource_id',
        'ip_address', 'user_agent', 'metadata', 'created_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function account()
    {
        return $this->belongsTo(\App\Domain\Identity\Models\Account::class, 'account_id');
    }
}


