<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Game;
use App\State\State;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class StateManager
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function getState(Game $game): State
    {
        $serialized = $game->getState();
        $this->logger->debug('Game state getter', [
            'game' => $game->getId(),
            'state' => $serialized,
        ]);

        return $this->serializer->deserialize($serialized, State::class, 'json');
    }

    public function setState(Game $game, State $state): void
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
     * @param \Closure(State): mixed $closure
     */
    public function withState(Game $game, \Closure $closure): void
    {
        $state = $this->getState($game);
        $closure($state);
        $this->setState($game, $state);
    }
}
