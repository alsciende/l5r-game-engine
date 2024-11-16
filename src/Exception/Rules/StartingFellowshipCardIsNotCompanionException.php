<?php

declare(strict_types=1);

namespace App\Exception\Rules;

use App\Entity\PhysicalCard;

class StartingFellowshipCardIsNotCompanionException extends RulesException
{
    public function __construct(
        private readonly PhysicalCard $card,
    ) {
        parent::__construct("Card {$card->getTitle()} is not a Companion");
    }

    public function getCard(): PhysicalCard
    {
        return $this->card;
    }
}
