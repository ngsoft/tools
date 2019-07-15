<?php

namespace NGSOFT\Tools\Cache\Exceptions;

use NGSOFT\Tools\Exceptions\InvalidArgumentException,
    Psr\Log\LoggerInterface;

class InvalidArgument extends InvalidArgumentException implements \Psr\Cache\InvalidArgumentException, \Psr\SimpleCache\InvalidArgumentException {

}
