<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';
    protected $primaryKey = 'notification_id';

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'notification_type_id',
        'is_read',
        'metadata',
        'sent_at',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'metadata' => 'array',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    /**
     * علاقة المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * علاقة نوع الإشعار من القيم المحددة
     */
    public function type()
    {
        return $this->belongsTo(LookupValue::class, 'notification_type_id', 'lookup_value_id');
    }

    /**
     * نطاق الإشعارات غير المقروءة
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * نطاق الإشعارات المقروءة
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * نطاق الإشعارات المرسلة بعد تاريخ معين
     */
    public function scopeSince($query, $date)
    {
        return $query->where('sent_at', '>=', $date);
    }

    /**
     * تحديد الإشعار كمقروء
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * تحديد الإشعار كغير مقروء
     */
    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * إنشاء إشعار جديد
     */
    public static function createNotification($userId, $title, $message, $typeId, $metadata = null)
    {
        return self::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'notification_type_id' => $typeId,
            'metadata' => $metadata,
            'sent_at' => now(),
        ]);
    }

    /**
     * الحصول على بيانات إضافية من metadata
     */
    public function getMetadataValue($key, $default = null)
    {
        return $this->metadata[$key] ?? $default;
    }
}
