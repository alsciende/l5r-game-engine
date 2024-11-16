<?php

declare(strict_types=1);

namespace App\Model;

class CardData
{
    public int $id;
    public int $set;
    public string $rarity;
    public int $number;
    public string $collectorsInfo;
    public int $unique;
    public string $title;
    public ?string $subtitle = null;
    public ?string $culture = null;
    public string $type;
    public ?string $race = null;
    public ?string $class = null;
    public ?int $twilightCost = null;
    public ?int $strength = null;
    public ?int $vitality = null;
    public ?int $resistance = null;
    public ?int $minionSiteNumber = null;
    public ?int $allyHomeSite = null;
    public ?int $siteNumber = null;
    public ?string $siteArrow = null;
    public string $block;
    public string $background;
    public string $image;
    public ?string $topIcon = null;
    public ?int $topText = null;
    public ?string $middleIcon = null;
    public ?int $middleText = null;
    public ?string $bottomIcon = null;
    public ?string $bottomText = null;
    public ?string $text = null;
    public ?string $lore = null;
}
