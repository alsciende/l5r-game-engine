<?php

declare(strict_types=1);

namespace App\State;

class StartingFellowship
{
    public string $playerId;
    /**
     * @var string[]
     */
    public array $cards;

    /**
     * @param string[] $cards
     */
    public function __construct(string $playerId, array $cards)
    {
        $this->playerId = $playerId;
        $this->cards = $cards;
    }
}
