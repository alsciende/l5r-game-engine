<?php

namespace App\Tests\EventDispatcher;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\Event;

readonly class TestEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private TestAccumulator $accumulator,
        private int             $counter,
    )
    {
    }

    public function onFuBar(Event $event): void
    {
        $this->accumulator->addCounter($this->counter);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'fu.bar' => 'onFuBar',
        ];
    }
}