<?php

namespace NGSOFT\Tools\Exceptions;

use Exception;
use NGSOFT\Tools\Interfaces\LogWriterInterface;
use NGSOFT\Tools\Traits\Logger;
use Psr\Log\LoggerAwareTrait;

class Exception extends Exception implements LogWriterInterface {

    use LoggerAwareTrait,
        Logger;

    public function getLog() {

        return [
        ];
    }

}
