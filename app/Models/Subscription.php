<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';
    protected $primaryKey = 'subscription_id';

    protected $fillable = [
        'user_id',
        'plan_id',
        'start_date',
        'end_date',
        'renewal_date',
        'auto_renew',
        'subscription_status_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'renewal_date' => 'date',
        'auto_renew' => 'boolean',
    ];

    /**
     * علاقة المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * علاقة الخطة
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id', 'plan_id');
    }

    /**
     * علاقة حالة الاشتراك من القيم المحددة
     */
    public function status()
    {
        return $this->belongsTo(LookupValue::class, 'subscription_status_id', 'lookup_value_id');
    }

    /**
     * علاقة الفواتير الخاصة بالاشتراك
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'subscription_id', 'subscription_id');
    }

    /**
     * نطاق الاشتراكات النشطة (حتى تاريخ اليوم)
     */
    public function scopeActive($query)
    {
        return $query->where('end_date', '>=', now())
                     ->orWhere('auto_renew', true);
    }

    /**
     * نطاق الاشتراكات المنتهية
     */
    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now())
                     ->where('auto_renew', false);
    }

    /**
     * التحقق مما إذا كان الاشتراك نشطاً
     */
    public function isActive()
    {
        return $this->end_date >= now() || $this->auto_renew;
    }

    /**
     * تجديد الاشتراك
     */
    public function renew($period = 'month')
    {
        $this->start_date = now();
        $this->end_date = $period === 'year'
            ? now()->addYear()
            : now()->addMonth();
        $this->renewal_date = $this->end_date->subDays(7); // تجديد قبل 7 أيام من الانتهاء
        $this->save();
    }
}
