<?php

namespace NGSOFT\Tools\Exceptions;

use NGSOFT\Tools\{
    Interfaces\ExceptionInterface, Traits\ExceptionLoggerTrait
};

class ErrorException extends \ErrorException implements ExceptionInterface {

    use ExceptionLoggerTrait;
}
