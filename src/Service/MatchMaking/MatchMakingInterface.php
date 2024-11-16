<?php

declare(strict_types=1);

namespace App\Service\MatchMaking;

interface MatchMakingInterface
{
    public function findMatches(): void;
}
