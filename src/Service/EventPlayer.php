<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Event;
use App\Message\ActionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;

readonly class EventPlayer
{
    public function __construct(
        private SerializerInterface $serializer,
        private MessageBusInterface $bus,
    ) {
    }

    public function playEvent(Event $event): void
    {
        $message = $this->serializer->deserialize(
            data: $event->getPayload(),
            type: $event->getName(),
            format: 'json',
        );

        if (! $message instanceof ActionInterface) {
            throw new \LogicException("Event payload class {$event->getName()} does not implement " . ActionInterface::class);
        }

        $this->bus->dispatch($message);
    }
}
