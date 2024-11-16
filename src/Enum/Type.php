<?php

declare(strict_types=1);

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum Type: string implements TranslatableInterface
{
    case ATTACHMENT = 'attachment';
    case CHARACTER = 'character';
    case EVENT = 'event';
    case HOLDING = 'holding';
    case PROVINCE = 'province';
    case ROLE = 'role';
    case STRONGHOLD = 'stronghold';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans($this->value, domain: 'types', locale: $locale);
    }
}
