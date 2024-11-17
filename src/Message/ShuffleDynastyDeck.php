<?php

declare(strict_types=1);

namespace App\Message;

readonly class ShuffleDynastyDeck implements PlayerActionInterface
{
    public function __construct(
        private string $gameId,
        private string $playerId,
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
}
