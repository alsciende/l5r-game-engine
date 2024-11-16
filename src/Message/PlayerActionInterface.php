<?php

declare(strict_types=1);

namespace App\Message;

interface PlayerActionInterface extends ActionInterface
{
    public function getGameId(): string;

    public function getPlayerId(): string;
}
