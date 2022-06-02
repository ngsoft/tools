<?php

declare(strict_types=1);

namespace NGSOFT\Exceptions;

use Psr\Container\{
    ContainerInterface, NotFoundExceptionInterface
};
use RuntimeException;

class NotFoundException extends RuntimeException implements NotFoundExceptionInterface
{

    /**
     * @param ContainerInterface $container
     * @param string $id
     */
    public function __construct(
            protected ContainerInterface $container,
            string $id,
    )
    {
        parent::__construct(sprintf('%s Entry ID "%s" not found.', get_class($container), $id));
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

}
