<?php

declare(strict_types=1);

namespace App\Notification;

class NotificationEvent
{
    /**
     * @var array<string,mixed>
     */
    private array $data = [];

    public function __construct(
        private readonly string $eventType,
    ) {
    }

    public function add(string $key, mixed $value): self
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * @return array<string,mixed>
     */
    public function export(): array
    {
        $this->data['event'] = $this->eventType;

        return $this->data;
    }
}
