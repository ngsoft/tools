<?php

declare(strict_types=1);

namespace NGSOFT\Timer;

class StopWatchResult
{

    public function create(int|float $seconds = 0): static
    {
        return new static($seconds);
    }

    public function __construct(protected readonly int|float $seconds)
    {

    }

}
