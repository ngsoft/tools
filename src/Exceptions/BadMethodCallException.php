<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Exceptions;

use NGSOFT\Tools\{
    Interfaces\ExceptionInterface, Traits\ExceptionLoggerTrait
};

class BadMethodCallException extends \BadMethodCallException implements ExceptionInterface {

    use ExceptionLoggerTrait;
}
