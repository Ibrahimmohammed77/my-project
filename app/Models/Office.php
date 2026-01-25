<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $primaryKey = 'office_id';

    protected $fillable = ['studio_id', 'subscriber_id', 'name', 'description'];

    public function studio()
    {
        return $this->belongsTo(Studio::class, 'studio_id');
    }

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class, 'subscriber_id');
    }
}
