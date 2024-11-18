<?php

declare(strict_types=1);

namespace App\Message;

final readonly class GainHonor implements PlayerActionInterface
{
    public function __construct(
        private string $gameId,
        private string $playerId,
        private int $honorGain,
    ) {
    }

    public function getGameId(): string
    {
        return $this->gameId;
    }

    public function getPlayerId(): string
    {
        return $this->playerId;
    }

    public function getHonorGain(): int
    {
        return $this->honorGain;
    }
}
