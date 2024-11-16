<?php

declare(strict_types=1);

namespace App\Message;

readonly class BidTokens implements PlayerActionInterface
{
    public function __construct(
        private string $gameId,
        private string $playerId,
        private int $nbTokens,
    ) {
    }

    #[\Override]
    public function getGameId(): string
    {
        return $this->gameId;
    }

    #[\Override]
    public function getPlayerId(): string
    {
        return $this->playerId;
    }

    public function getNbTokens(): int
    {
        return $this->nbTokens;
    }
}
