<?php

declare(strict_types=1);

namespace NGSOFT\Exceptions;

use Psr\Container\{
    ContainerInterface, NotFoundExceptionInterface
};
use RuntimeException;

class NotFoundException extends RuntimeException implements NotFoundExceptionInterface {

    /** @var ContainerInterface */
    protected $container;

    /**
     * @param string $id Entry ID
     * @param ContainerInterface $container
     */
    public function __construct(
            string $id,
            ContainerInterface $container
    ) {
        $this->container = $container;
        parent::__construct(sprintf('Entry "%s" not found.', $id));
    }

    public function getContainer(): ContainerInterface {
        return $this->container;
    }

}
