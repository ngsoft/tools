<?php

namespace NGSOFT\Tools\Exceptions;

use Psr\Cache\InvalidArgumentException as I1;
use Psr\SimpleCache\InvalidArgumentException as I2;
use InvalidArgumentException;

class BasicCacheInvalidKey extends InvalidArgumentException implements I1, I2 {

}
