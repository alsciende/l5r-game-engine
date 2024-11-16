<?php

declare(strict_types=1);

namespace App\Exception\Data;

class CardNotFoundException extends NotFoundException
{
    public function __construct(
        private readonly string $cardId,
    ) {
        parent::__construct("Card not found: [{$this->cardId}]");
    }

    public function getCardId(): string
    {
        return $this->cardId;
    }
}
