<?php

declare(strict_types=1);

namespace App\Tests\EventDispatcher;

class TestAccumulator
{
    private int $total = 0;

    public function addCounter(int $counter): void
    {
        $this->total += $counter;
    }

    public function getTotal(): int
    {
        return $this->total;
    }
}
