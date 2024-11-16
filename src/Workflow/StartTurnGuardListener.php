<?php

declare(strict_types=1);

namespace App\Workflow;

use App\Entity\Game;
use App\Service\StateManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Workflow\Event\GuardEvent;

#[AsEventListener(event: 'workflow.game.guard.start_turn')]
readonly class StartTurnGuardListener
{
    public function __construct(
        private StateManager $stateManager,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(GuardEvent $event): void
    {
        /** @var Game $game */
        $game = $event->getSubject();

        $state = $this->stateManager->getState($game);
        if (count($state->startingFellowships) < 2) {
            $event->setBlocked(true, 'Not enough starting fellowships.');
        }

        $this->logger->debug('Game is ready to leave choose_fellowship', [
            'game' => $game->getId(),
            'game_state' => $game->getState(),
            'starting_fellowships' => count($state->startingFellowships),
        ]);
    }
}
