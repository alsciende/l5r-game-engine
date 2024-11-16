<?php

declare(strict_types=1);

namespace App\Notifier;

use App\Notification\NotificationInterface;

interface NotifierInterface
{
    public function notify(NotificationInterface $notification): void;
}
