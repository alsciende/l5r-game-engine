<?php

declare(strict_types=1);

namespace App\Message;

interface ActionInterface
{
    public function getGameId(): string;
}
