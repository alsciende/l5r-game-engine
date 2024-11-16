<?php

declare(strict_types=1);

namespace App\Service\GameLauncher;

use App\Entity\Candidate;

interface GameLauncherInterface
{
    /**
     * @param array<Candidate> $candidates
     */
    public function createGame(array $candidates): void;
}
