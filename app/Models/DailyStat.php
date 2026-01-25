<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyStat extends Model
{
    protected $primaryKey = 'stat_id';
    public $timestamps = false; // Only created_at

    protected $fillable = [
        'stat_date', 'account_id', 'new_accounts', 'new_photos',
        'photo_views', 'card_activations', 'revenue', 'created_at'
    ];

    protected $casts = [
        'stat_date' => 'date',
        'created_at' => 'datetime',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
