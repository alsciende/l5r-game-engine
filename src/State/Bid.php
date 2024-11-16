<?php

declare(strict_types=1);

namespace App\State;

class Bid
{
    public string $playerId;
    public int $bid;

    public function __construct(string $playerId, int $bid)
    {
        $this->playerId = $playerId;
        $this->bid = $bid;
    }
}
