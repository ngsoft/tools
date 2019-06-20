<?php

use Psr\Cache\InvalidArgumentException;
use Psr\SimpleCache\InvalidArgumentException as InvalidArgumentException2;

namespace NGSOFT\Tools\Exceptions;

class PSRCacheInvalidArgumentException extends InvalidArgumentException implements InvalidArgumentException, InvalidArgumentException2 {

}
