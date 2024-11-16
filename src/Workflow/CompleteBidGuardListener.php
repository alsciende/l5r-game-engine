<?php

declare(strict_types=1);

namespace App\Workflow;

use App\Entity\Game;
use App\Service\StateManager;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Workflow\Event\GuardEvent;

#[AsEventListener(event: 'workflow.game.guard.complete_bid')]
readonly class CompleteBidGuardListener
{
    public function __construct(
        private StateManager $stateManager,
    ) {
    }

    public function __invoke(GuardEvent $event): void
    {
        /** @var Game $game */
        $game = $event->getSubject();

        $state = $this->stateManager->getState($game);
        if (count($state->bids) < 2) {
            $event->setBlocked(true, 'Not enough bids.');
        }
    }
}
