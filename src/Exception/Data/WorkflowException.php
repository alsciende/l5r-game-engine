<?php

declare(strict_types=1);

namespace App\Exception\Data;

class WorkflowException extends DataException
{
    public function __construct(string $expectedPlace, string $actualPlace)
    {
        parent::__construct(sprintf(
            'The game is in the place %s but this action requires the game to be in the place %s.',
            $actualPlace,
            $expectedPlace
        ));
    }
}
