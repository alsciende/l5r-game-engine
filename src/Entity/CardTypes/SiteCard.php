<?php

declare(strict_types=1);

namespace App\Entity\CardTypes;

use App\Entity\Card;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class SiteCard extends Card
{
}
