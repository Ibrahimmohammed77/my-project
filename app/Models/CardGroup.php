<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardGroup extends Model
{
    protected $primaryKey = 'group_id';
    protected $fillable = ['name', 'description'];

    public function cards()
    {
        return $this->hasMany(Card::class, 'card_group_id');
    }
}
