<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Events\UserUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWelcomeEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(UserCreated $event): void
    {
        // إرسال بريد ترحيبي
        // Mail::to($event->user->email)->send(new WelcomeEmail($event->user));
    }

    public function failed(UserCreated $event, $exception): void
    {
        // معالجة الفشل في إرسال البريد
    }
}

class UpdateUserStatistics implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(UserUpdated $event): void
    {
        // تحديث الإحصائيات
        if (isset($event->changes['user_type_id'])) {
            // تحديث إحصائيات النوع
        }
    }
}
