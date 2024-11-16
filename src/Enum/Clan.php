<?php

declare(strict_types=1);

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum Clan: string implements TranslatableInterface
{
    case CRAB = 'crab';
    case CRANE = 'crane';
    case DRAGON = 'dragon';
    case LION = 'lion';
    case NEUTRAL = 'neutral';
    case PHOENIX = 'phoenix';
    case SCORPION = 'scorpion';
    case UNICORN = 'unicorn';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans($this->value, domain: 'clans', locale: $locale);
    }
}
