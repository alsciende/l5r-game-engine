<?php

declare(strict_types=1);

namespace App\Notifier;

use App\Notification\NotificationInterface;
use Psr\Log\LoggerInterface;

readonly class LoggerNotifier implements NotifierInterface
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    #[\Override]
    public function notify(NotificationInterface $notification): void
    {
        $this->logger->debug(
            'Notification',
            [
                'topics' => $notification->getTopics(),
                'data' => $notification->getData(),
            ]
        );
    }
}
