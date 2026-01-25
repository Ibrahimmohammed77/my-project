<?php

namespace App\Domain\Shared\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'setting_id';

    protected $fillable = [
        'setting_key', 'setting_value', 'setting_type_id', 'description', 'is_public'
    ];

    protected $casts = [
        'setting_value' => 'array', // Or mixed, but schema says JSON
        'is_public' => 'boolean',
    ];

    public function type()
    {
        return $this->belongsTo(LookupValue::class, 'setting_type_id');
    }
}

