<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';
    protected $primaryKey = 'log_id';

    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'action',
        'resource_type',
        'resource_id',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * علاقة المستخدم (اختيارية - يمكن أن تكون null)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * علاقة مرنة مع المورد
     */
    public function resource()
    {
        return $this->morphTo(null, 'resource_type', 'resource_id');
    }

    /**
     * نطاق الأنشطة لمستخدم معين
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * نطاق الأنشطة لنوع مورد معين
     */
    public function scopeForResourceType($query, $resourceType)
    {
        return $query->where('resource_type', $resourceType);
    }

    /**
     * نطاق الأنشطة لفعل معين
     */
    public function scopeForAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * نطاق الأنشطة ضمن نطاق تاريخي
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * تسجيل نشاط جديد (دالة ثابتة)
     */
    public static function log(
        $userId,
        $action,
        $resourceType = null,
        $resourceId = null,
        $metadata = null,
        $ip = null,
        $userAgent = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'ip_address' => $ip ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * تسجيل نشاط للمستخدم الحالي
     */
    public static function logCurrent(
        $action,
        $resourceType = null,
        $resourceId = null,
        $metadata = null
    ): ?self {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        return self::log(
            $user->id,
            $action,
            $resourceType,
            $resourceId,
            $metadata
        );
    }

    /**
     * تسجيل نشاط النظام
     */
    public static function logSystem(
        $action,
        $resourceType = null,
        $resourceId = null,
        $metadata = null
    ): self {
        return self::create([
            'user_id' => null,
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * الحصول على وصف نصي للنشاط
     */
    public function getDescriptionAttribute(): string
    {
        $descriptions = [
            'login' => 'تسجيل دخول إلى النظام',
            'logout' => 'تسجيل خروج من النظام',
            'user_created' => 'إنشاء مستخدم جديد',
            'user_updated' => 'تحديث بيانات مستخدم',
            'user_deleted' => 'حذف مستخدم',
            'album_created' => 'إنشاء ألبوم جديد',
            'photo_uploaded' => 'رفع صورة جديدة',
            'card_activated' => 'تفعيل بطاقة',
            'payment_received' => 'استلام دفعة مالية',
        ];

        return $descriptions[$this->action] ?? "قام بـ {$this->action} في {$this->resource_type}";
    }
}
