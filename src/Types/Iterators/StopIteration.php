<?php

declare(strict_types=1);

namespace NGSOFT\Types\Iterators;

class StopIteration extends \OutOfRangeException
{

    public function __construct(string $message = "Iteration has been stopped")
    {
        parent::__construct($message);
    }

}
