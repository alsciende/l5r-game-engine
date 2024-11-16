<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Game;
use App\State\GameState;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;

readonly class GameStateManager
{
    public function __construct(
        private SerializerInterface $serializer,
        private LoggerInterface $logger,
    ) {
    }

    public function getState(Game $game): GameState
    {
        $serialized = $game->getState();
        $this->logger->debug('Game state getter', [
            'game' => $game->getId(),
            'state' => $serialized,
        ]);

        return $this->serializer->deserialize($serialized, GameState::class, 'json');
    }

    public function setState(Game $game, GameState $state): void
    {
        $serialized = $this->serializer->serialize($state, 'json');
        $this->logger->debug('Game state setter', [
            'game' => $game->getId(),
            'before' => $game->getState(),
            'after' => $serialized,
        ]);

        $game->setState($serialized);
    }

    /**
     * @param \Closure(GameState): mixed $closure
     */
    public function withState(Game $game, \Closure $closure): void
    {
        $state = $this->getState($game);
        $closure($state);
        $this->setState($game, $state);
    }
}
