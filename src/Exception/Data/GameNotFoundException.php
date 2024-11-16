<?php

declare(strict_types=1);

namespace App\Exception\Data;

class GameNotFoundException extends NotFoundException
{
    public function __construct(
        private readonly string $gameId,
    ) {
        parent::__construct("Game not found: [{$this->gameId}]");
    }

    public function getGameId(): string
    {
        return $this->gameId;
    }
}
