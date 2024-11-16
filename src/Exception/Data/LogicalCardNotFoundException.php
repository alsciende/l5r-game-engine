<?php

declare(strict_types=1);

namespace App\Exception\Data;

class LogicalCardNotFoundException extends NotFoundException
{
    public function __construct(
        private readonly string $logicalCardId,
    ) {
        parent::__construct("Logical card not found: [{$this->logicalCardId}]");
    }

    public function getLogicalCardId(): string
    {
        return $this->logicalCardId;
    }
}
