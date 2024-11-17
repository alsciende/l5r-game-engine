<?php

declare(strict_types=1);

namespace App\Exception\Rules;

class MinimumHonorReachedException extends RulesException
{
    public function __construct()
    {
        parent::__construct('Player has reached minimum honor.');
    }
}
