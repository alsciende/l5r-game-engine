<?php

declare(strict_types=1);

namespace App\Exception\Data;

class PlayerNotFoundException extends NotFoundException
{
    public function __construct(
        private readonly string $playerId,
    ) {
        parent::__construct("Player not found: [{$this->playerId}]");
    }

    public function getPlayerId(): string
    {
        return $this->playerId;
    }
}
