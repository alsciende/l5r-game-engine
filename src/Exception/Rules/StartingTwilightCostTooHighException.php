<?php

declare(strict_types=1);

namespace App\Exception\Rules;

class StartingTwilightCostTooHighException extends RulesException
{
    public function __construct(
        public readonly int $twilightCost,
    ) {
        parent::__construct("The total twilight cost of your cost is {$twilightCost}, greater than the allowed maximum of 4.");
    }

    public function getTwilightCost(): int
    {
        return $this->twilightCost;
    }
}
