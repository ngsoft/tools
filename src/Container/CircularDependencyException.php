<?php

declare(strict_types=1);

namespace NGSOFT\Container;

class CircularDependencyException extends \RuntimeException implements \Psr\Container\ContainerExceptionInterface
{

}
