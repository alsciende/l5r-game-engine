<?php

declare(strict_types=1);

namespace App\Workflow;

use App\Entity\Game;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Workflow\Event\GuardEvent;

#[AsEventListener(event: 'workflow.game.guard.start_game')]
readonly class StartGameGuardListener
{
    public function __invoke(GuardEvent $event): void
    {
        /** @var Game $game */
        $game = $event->getSubject();

        if ($game->getPlayers()->count() < 2) {
            $event->setBlocked(true, 'Not enough players.');
        }
    }
}
