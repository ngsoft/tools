<?php

declare(strict_types=1);

namespace NGSOFT\Exceptions;

use OutOfRangeException,
    Throwable;

class StopIteration extends OutOfRangeException
{

    public function __construct(string $message = "Iteration has been stopped", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
