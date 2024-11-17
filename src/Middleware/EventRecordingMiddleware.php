<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Entity\Event;
use App\Message\ActionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Record the message as an Event after its handling.
 */
readonly class EventRecordingMiddleware implements MiddlewareInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
    ) {
    }

    #[\Override]
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        // forward message and use new envelope
        $envelope = $stack->next()->handle($envelope, $stack);

        $message = $envelope->getMessage();

        if ($message instanceof ActionInterface) {
            $this->recordAction($message);
        }

        return $envelope;
    }

    private function recordAction(ActionInterface $action): void
    {
        $name = $action::class;
        $payload = $this->serializer->serialize($action, 'json');

        $this->entityManager->persist(new Event($name, $payload));
    }
}
