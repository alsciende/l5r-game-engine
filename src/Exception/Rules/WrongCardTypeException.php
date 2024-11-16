<?php

declare(strict_types=1);

namespace App\Exception\Rules;

use App\Entity\PhysicalCard;
use App\Enum\Type;

class WrongCardTypeException extends RulesException
{
    public function __construct(
        PhysicalCard $card,
        Type $expectedType,
    ) {
        parent::__construct(sprintf(
            'Card [%s] of type [%s] is not of expected type [%s]',
            $card->getId(),
            $card->getLogicalCard()->getType()->value,
            $expectedType->value,
        ));
    }
}
