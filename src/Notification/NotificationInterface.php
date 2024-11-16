<?php

declare(strict_types=1);

namespace App\Notification;

interface NotificationInterface
{
    /**
     * @return array<string>
     */
    public function getTopics(): array;

    /**
     * @return array<string,mixed>
     */
    public function getData(): array;
}
