<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Player;
use App\State\PlayerState;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;

readonly class PlayerStateManager
{
    public function __construct(
        private SerializerInterface $serializer,
        private LoggerInterface $logger,
    ) {
    }

    public function getState(Player $player): PlayerState
    {
        $serialized = $player->getState();
        $this->logger->debug('Player state getter', [
            'player' => $player->getId(),
            'state' => $serialized,
        ]);

        return $this->serializer->deserialize($serialized, PlayerState::class, 'json');
    }

    public function setState(Player $player, PlayerState $state): void
    {
        $serialized = $this->serializer->serialize($state, 'json');
        $this->logger->debug('Player state setter', [
            'player' => $player->getId(),
            'before' => $player->getState(),
            'after' => $serialized,
        ]);
        $this->serializer->deserialize($serialized, PlayerState::class, 'json');

        $player->setState($serialized);
    }

    /**
     * @param \Closure(PlayerState): mixed $closure
     */
    public function withState(Player $player, \Closure $closure): void
    {
        $state = $this->getState($player);
        $closure($state);
        $this->setState($player, $state);
    }
}
