<?php

declare(strict_types=1);

namespace App\Notification;

abstract class AbstractNotification implements NotificationInterface
{
    /**
     * @var array<string>
     */
    private array $topics = [];

    /**
     * @var array<string,mixed>
     */
    private array $data = [];

    protected function addTopic(string $url): void
    {
        $this->topics[] = $url;
    }

    protected function setEvent(NotificationEvent $event): void
    {
        $this->data = $event->export();
    }

    /**
     * @return array<string>
     */
    #[\Override]
    public function getTopics(): array
    {
        return $this->topics;
    }

    /**
     * @return array<string,mixed>
     */
    #[\Override]
    public function getData(): array
    {
        return $this->data;
    }
}
