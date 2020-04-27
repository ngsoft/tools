<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Exceptions;

use NGSOFT\Tools\{
    Interfaces\ExceptionInterface, Traits\ExceptionLoggerTrait
};

class InvalidArgumentException extends \InvalidArgumentException implements ExceptionInterface {

    use ExceptionLoggerTrait;
}
