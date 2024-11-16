<?php

declare(strict_types=1);

namespace App\Entity\CardTypes;

use App\Entity\Card;

abstract class CharacterCard extends Card
{
    public function isExhausted(): bool
    {
        return $this->state[self::ATTR_CURRENT_VITALITY] - $this->state[self::ATTR_WOUNDS] > 1;
    }

    public function addWounds(int $quantity = 1): void
    {
        $this->state[self::ATTR_WOUNDS] = $this->state[self::ATTR_WOUNDS] + 1;
    }

    public function resetState(): void
    {
        $this->state[self::ATTR_WOUNDS] = 0;
        $this->state[self::ATTR_CURRENT_VITALITY] = $this->state[self::ATTR_BASE_VITALITY];
    }
}
