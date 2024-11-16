<?php

declare(strict_types=1);

namespace App\Workflow;

use App\Entity\Game;
use Psr\Log\LoggerInterface;
use Symfony\Component\Workflow\Attribute\AsTransitionListener;
use Symfony\Component\Workflow\Event\TransitionEvent;

#[AsTransitionListener(workflow: 'game', transition: 'start_game')]
readonly class StartGameTransitionListener
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(TransitionEvent $event): void
    {
        /** @var Game $game */
        $game = $event->getSubject();

        $this->logger->debug('this is where we should setup the game', [
            'game_id' => $game->getId(),
        ]);
    }
}
