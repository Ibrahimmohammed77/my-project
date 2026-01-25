<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageAccount extends Model
{
    protected $primaryKey = 'storage_account_id';

    protected $fillable = [
        'owner_type', 'owner_id', 'total_space', 'used_space', 'status'
    ];

    public function owner()
    {
        return $this->morphTo();
    }
}
