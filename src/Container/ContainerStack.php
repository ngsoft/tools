<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use LogicException;
use Psr\Container\{
    ContainerExceptionInterface, ContainerInterface
};

class ContainerStack
{

    public function __construct(protected ContainerInterface $container, protected self $next = null)
    {
        if ($container === $next) {
            throw new LogicException(sprintf('Cannot stack the same container %s#%d on top of one another.', get_class($container), spl_object_id($container)));
        }
    }

    /** {@inheritdoc} */
    public function get(string $id): mixed
    {
        try {
            return $this->container->get($id);
        } catch (ContainerExceptionInterface $error) {
            if ($this->next) return $this->next->get($id);
            throw $error;
        }
    }

    /** {@inheritdoc} */
    public function has(string $id): bool
    {
        return $this->container->has($id) || ($this->next?->has($id) ?? false);
    }

}
