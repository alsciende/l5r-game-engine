<?php

declare(strict_types=1);

namespace App\Exception\Rules;

class InvalidTokenBidException extends RulesException
{
    public function __construct(
        public readonly int $bid,
    ) {
        parent::__construct("Bidding {$this->bid} tokens is not allowed.");
    }
}
