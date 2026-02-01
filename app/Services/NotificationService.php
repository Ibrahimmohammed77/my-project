<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\LookupValue;

class NotificationService
{
    /**
     * Send notification to user.
     */
    public function send(
        int $userId,
        string $title,
        string $message,
        string $typeCode,
        array $metadata = null
    ): Notification {
        $type = LookupValue::where('code', $typeCode)->first();

        if (!$type) {
            $master = \App\Models\LookupMaster::where('code', 'NOTIFICATION_TYPE')->first();
            
            if (!$master) {
                // Fallback or log error
                throw new \Exception('Lookup master for NOTIFICATION_TYPE not found.');
            }

            // Create default type if not exists
            $type = LookupValue::create([
                'lookup_master_id' => $master->lookup_master_id,
                'code' => $typeCode,
                'name' => ucfirst(str_replace('_', ' ', $typeCode)),
                'description' => 'نوع إشعار ' . $typeCode,
                'is_active' => true,
                'sort_order' => 99,
            ]);
        }

        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'notification_type_id' => $type->lookup_value_id,
            'metadata' => $metadata,
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(int $notificationId): bool
    {
        $notification = Notification::find($notificationId);

        if ($notification && !$notification->is_read) {
            return $notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return false;
    }

    /**
     * Get user notifications.
     */
    public function getUserNotifications(
        int $userId,
        bool $unreadOnly = false,
        int $limit = 20,
        int $offset = 0
    ) {
        $query = Notification::where('user_id', $userId)
            ->with('type')
            ->orderBy('sent_at', 'desc');

        if ($unreadOnly) {
            $query->where('is_read', false);
        }

        return $query->skip($offset)
            ->take($limit)
            ->get();
    }

    /**
     * Get unread notifications count.
     */
    public function getUnreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }
}
