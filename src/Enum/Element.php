<?php

declare(strict_types=1);

namespace App\Enum;

enum Element: string
{
    case AIR = 'air';
    case EARTH = 'earth';
    case FIRE = 'fire';
    case VOID = 'void';
    case WATER = 'water';
}
