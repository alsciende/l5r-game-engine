<?php

declare(strict_types=1);

namespace App\Exception\Rules;

class DuplicateStartingCompanionException extends RulesException
{
    public function __construct(
        public readonly string $title,
    ) {
        parent::__construct("Starting fellowship contains duplicates of unique companion {$title}");
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
