<?php

declare(strict_types=1);

namespace App\Workflow;

use App\Entity\Game;
use App\Entity\Player;
use App\Service\GameStateManager;
use App\State\GameState;
use Psr\Log\LoggerInterface;
use Symfony\Component\Workflow\Attribute\AsTransitionListener;
use Symfony\Component\Workflow\Event\TransitionEvent;

#[AsTransitionListener(workflow: 'game', transition: 'start_game')]
readonly class StartGameTransitionListener
{
    public function __construct(
        private GameStateManager $stateManager,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Select first player.
     */
    public function __invoke(TransitionEvent $event): void
    {
        /** @var Game $game */
        $game = $event->getSubject();

        $this->stateManager->withState(
            $game,
            fn (GameState $state) => $state->setFirstPlayer($this->selectRandomFirstPlayer($game))
        );

        $this->logger->debug('StartGameTransitionListener', [
            'first_player_id' => $this->stateManager->getState($game)->firstPlayerId,
        ]);
    }

    private function selectRandomFirstPlayer(Game $game): Player
    {
        $players = $game->getPlayers()->toArray();

        return $players[array_rand($players)];
    }
}
