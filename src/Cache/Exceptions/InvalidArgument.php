<?php

namespace NGSOFT\Tools\Cache\Exceptions;

use NGSOFT\Tools\Exceptions\InvalidArgumentException;

class InvalidArgument extends InvalidArgumentException implements \Psr\Cache\InvalidArgumentException, \Psr\SimpleCache\InvalidArgumentException {

}
