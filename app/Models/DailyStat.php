<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyStat extends Model
{
    use HasFactory;

    protected $table = 'daily_stats';
    protected $primaryKey = 'stat_id';

    protected $fillable = [
        'stat_date',
        'user_id',
        'new_users',
        'new_photos',
        'photo_views',
        'card_activations',
        'revenue',
    ];

    protected $casts = [
        'stat_date' => 'date',
        'new_users' => 'integer',
        'new_photos' => 'integer',
        'photo_views' => 'integer',
        'card_activations' => 'integer',
        'revenue' => 'decimal:2',
    ];

    /**
     * علاقة المستخدم (اختيارية)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * نطاق الإحصائيات لتاريخ معين
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('stat_date', $date);
    }

    /**
     * نطاق الإحصائيات لمستخدم معين
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * نطاق الإحصائيات ضمن نطاق تاريخي
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('stat_date', [$startDate, $endDate]);
    }

    /**
     * نطاق الإحصائيات العامة (بدون مستخدم)
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * الحصول على إحصائيات اليوم
     */
    public static function getTodayStats($userId = null)
    {
        return self::firstOrCreate(
            [
                'stat_date' => today(),
                'user_id' => $userId,
            ],
            [
                'new_users' => 0,
                'new_photos' => 0,
                'photo_views' => 0,
                'card_activations' => 0,
                'revenue' => 0,
            ]
        );
    }

    /**
     * زيادة عدد المستخدمين الجدد
     */
    public function incrementNewUsers($count = 1)
    {
        $this->increment('new_users', $count);
    }

    /**
     * زيادة عدد الصور الجديدة
     */
    public function incrementNewPhotos($count = 1)
    {
        $this->increment('new_photos', $count);
    }

    /**
     * زيادة عدد مشاهدات الصور
     */
    public function incrementPhotoViews($count = 1)
    {
        $this->increment('photo_views', $count);
    }

    /**
     * زيادة عدد تنشيطات البطاقات
     */
    public function incrementCardActivations($count = 1)
    {
        $this->increment('card_activations', $count);
    }

    /**
     * زيادة الإيرادات
     */
    public function incrementRevenue($amount)
    {
        $this->increment('revenue', $amount);
    }

    /**
     * الحصول على إحصائيات مجمعة
     */
    public static function getAggregatedStats($startDate, $endDate, $userId = null)
    {
        $query = self::dateRange($startDate, $endDate);

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->whereNull('user_id');
        }

        return $query->selectRaw('
            SUM(new_users) as total_users,
            SUM(new_photos) as total_photos,
            SUM(photo_views) as total_views,
            SUM(card_activations) as total_activations,
            SUM(revenue) as total_revenue,
            AVG(new_users) as avg_users_per_day,
            AVG(new_photos) as avg_photos_per_day
        ')->first();
    }

    /**
     * تحديث إحصائيات متعددة دفعة واحدة
     */
    public function updateMultipleStats(array $stats)
    {
        $updates = [];

        foreach (['new_users', 'new_photos', 'photo_views', 'card_activations', 'revenue'] as $field) {
            if (isset($stats[$field])) {
                $updates[$field] = $this->$field + $stats[$field];
            }
        }

        if (!empty($updates)) {
            $this->update($updates);
        }
    }

    /**
     * الحصول على الإحصائيات كصفيف
     */
    public function toStatsArray()
    {
        return [
            'date' => $this->stat_date->format('Y-m-d'),
            'new_users' => $this->new_users,
            'new_photos' => $this->new_photos,
            'photo_views' => $this->photo_views,
            'card_activations' => $this->card_activations,
            'revenue' => (float) $this->revenue,
        ];
    }
}
