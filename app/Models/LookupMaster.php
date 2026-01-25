<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LookupMaster extends Model
{
    protected $primaryKey = 'lookup_master_id';
    protected $fillable = ['code', 'name', 'description'];

    public function values()
    {
        return $this->hasMany(LookupValue::class, 'lookup_master_id');
    }
}
