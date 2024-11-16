<?php

declare(strict_types=1);

namespace App\State;

class State
{
    /**
     * @var Bid[]
     */
    public array $bids = [];

    /**
     * @var StartingFellowship[]
     */
    public array $startingFellowships = [];

    public function addBid(Bid $bid): self
    {
        $this->bids[] = $bid;

        return $this;
    }

    public function addStartingFellowship(StartingFellowship $startingFellowship): self
    {
        $this->startingFellowships[] = $startingFellowship;

        return $this;
    }
}
