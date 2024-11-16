<?php

declare(strict_types=1);

namespace App\Tests\EventDispatcher;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\Event;

class EventDispatcherTest extends KernelTestCase
{
    /**
     * Testing that when we add a subscriber to the container event_dispatcher, it is called.
     */
    public function testRegistered(): void
    {
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());

        /** @var EventDispatcher $dispatcherService */
        $dispatcherService = static::getContainer()->get('event_dispatcher');

        $accumulator = new TestAccumulator();

        for ($i = 0; $i < 4; ++$i) {
            $dispatcherService->addSubscriber(new TestEventSubscriber($accumulator, $i));
        }

        $dispatcherService->dispatch(new Event(), 'fu.bar');

        $this->assertSame(6, $accumulator->getTotal());
    }
}
