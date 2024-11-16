<?php

declare(strict_types=1);

namespace App\Exception\Data;

class SourceNotFoundException extends NotFoundException
{
    public function __construct(
        private readonly int $source,
    ) {
        parent::__construct("Source not found: [{$this->source}]");
    }

    public function getSource(): int
    {
        return $this->source;
    }
}
