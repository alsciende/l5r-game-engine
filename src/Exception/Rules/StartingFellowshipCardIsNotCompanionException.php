<?php

declare(strict_types=1);

namespace App\Exception\Rules;

use App\Entity\Card;

class StartingFellowshipCardIsNotCompanionException extends RulesException
{
    public function __construct(
        private readonly Card $card,
    ) {
        parent::__construct("Card {$card->getTitle()} is not a Companion");
    }

    public function getCard(): Card
    {
        return $this->card;
    }
}
