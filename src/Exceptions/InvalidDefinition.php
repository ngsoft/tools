<?php

declare(strict_types=1);

namespace NGSOFT\Exceptions;

use NGSOFT\Container\Container,
    Psr\Container\ContainerExceptionInterface,
    ValueError;

class InvalidDefinition extends ValueError implements ContainerExceptionInterface {

    public function __construct(protected Container $container, $id, $value) {
        parent::__construct(
                sprintf('Invalid value definition array<string,mixed> expected, array<%s,%s> given.',
                        get_debug_type($id), get_debug_type($value))
        );
    }

    public function getContainer(): Container {
        return $this->container;
    }

}
