<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $primaryKey = 'commission_id';

    protected $fillable = [
        'studio_id', 'office_id', 'invoice_id', 'transaction_type_id',
        'amount', 'commission_rate', 'studio_share', 'platform_share',
        'settlement_date', 'commission_status_id'
    ];

    protected $casts = [
        'settlement_date' => 'date',
    ];

    public function studio()
    {
        return $this->belongsTo(Studio::class, 'studio_id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function transactionType()
    {
        return $this->belongsTo(LookupValue::class, 'transaction_type_id');
    }

    public function status()
    {
        return $this->belongsTo(LookupValue::class, 'commission_status_id');
    }
}
