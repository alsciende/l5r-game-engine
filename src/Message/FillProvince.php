<?php

declare(strict_types=1);

namespace App\Message;

final readonly class FillProvince implements PlayerActionInterface
{
    public function __construct(
        private string $gameId,
        private string $playerId,
        private string $provinceId,
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

    public function getProvinceId(): string
    {
        return $this->provinceId;
    }
}
