<?php

declare(strict_types=1);

namespace App\State;

use App\Entity\PhysicalCard;

class PlayerState
{
    public ?string $strongholdProvinceId = null;

    public function setStrongholdProvince(PhysicalCard $card): self
    {
        $this->strongholdProvinceId = $card->getId();

        return $this;
    }
}
