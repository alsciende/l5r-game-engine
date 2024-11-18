<?php

declare(strict_types=1);

namespace App\Message;

final readonly class DrawConflictCards implements PlayerActionInterface
{
    public function __construct(
        private string $gameId,
        private string $playerId,
        private int $quantity,
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

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
