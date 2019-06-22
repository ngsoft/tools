<?php

namespace NGSOFT\Tools\Exceptions;

use InvalidArgumentException;

class BasicCacheInvalidKey extends InvalidArgumentException implements \Psr\Cache\InvalidArgumentException, \Psr\SimpleCache\InvalidArgumentException {

}
