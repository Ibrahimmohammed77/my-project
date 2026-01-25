<?php

namespace App\Domain\Communications\Repositories\Eloquent;

use App\Domain\Shared\Repositories\Eloquent\BaseRepository;
use App\Domain\Communications\Repositories\Contracts\NotificationRepositoryInterface;
use App\Domain\Communications\Models\Notification;

class NotificationRepository extends BaseRepository implements NotificationRepositoryInterface
{
    public function __construct(Notification $notification)
    {
        parent::__construct(\Notification);
    }
}
