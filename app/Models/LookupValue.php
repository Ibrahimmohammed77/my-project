<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LookupValue extends Model
{
    protected $primaryKey = 'lookup_value_id';
    protected $fillable = ['lookup_master_id', 'code', 'name', 'description', 'is_active', 'sort_order'];

    public function master()
    {
        return $this->belongsTo(LookupMaster::class, 'lookup_master_id');
    }
}
