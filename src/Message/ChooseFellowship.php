<?php

declare(strict_types=1);

namespace App\Message;

readonly class ChooseFellowship implements PlayerActionInterface
{
    /**
     * @param array<string> $fellowship
     */
    public function __construct(
        private string $gameId,
        private string $playerId,
        private array $fellowship,
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

    /**
     * @return string[] card ids
     */
    public function getFellowship(): array
    {
        return $this->fellowship;
    }
}
