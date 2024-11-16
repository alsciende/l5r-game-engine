<?php

declare(strict_types=1);

namespace App\Enum;

enum Side: string
{
    case CONFLICT = 'conflict';
    case DYNASTY = 'dynasty';
    case PROVINCE = 'province';
    case ROLE = 'role';
}
