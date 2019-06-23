<?php

namespace NGSOFT\Tools\Exceptions;

use NGSOFT\Tools\Interfaces\ExceptionInterface;
use NGSOFT\Tools\Traits\Logger;
use Psr\Log\LoggerAwareTrait;

class RuntimeException extends \RuntimeException implements ExceptionInterface {

    use LoggerAwareTrait,
        Logger;
}
