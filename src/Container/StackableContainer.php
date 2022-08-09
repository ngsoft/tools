<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use InvalidArgumentException;
use NGSOFT\{
    Container\Exceptions\ContainerError, Container\Exceptions\NotFound, Traits\StringableObject
};
use Psr\Container\{
    ContainerExceptionInterface, ContainerInterface
};
use Stringable;
use function get_debug_type;

class StackableContainer implements ContainerInterface, Stringable
{

    use StringableObject;

    protected ContainerInterface $container;
    protected ?self $next = null;

    public function __construct(
            ContainerInterface|array $containers
    )
    {

        $containers = (array) $containers;

        if (empty($containers)) {
            throw new InvalidArgumentException('No container supplied');
        }


        foreach (array_values($containers) as $index => $container) {
            if ( ! ($container instanceof ContainerInterface)) {
                throw new InvalidArgumentException(sprintf('Invalid $containers[%d] type: %s expected, %s given', $index, ContainerInterface::class, get_debug_type($container)));
            }
            $this->addContainer($container);
        }
    }

    /**
     * Check if container already stacked
     *
     * @param ContainerInterface $container
     * @return bool
     */
    public function hasContainer(ContainerInterface $container): bool
    {

        if ($this->container === $container) {
            return true;
        }
        return $this->next?->hasContainer($container) ?? false;
    }

    /**
     * Stacks a new Container on top
     */
    public function addContainer(ContainerInterface $container): void
    {

        if ($container instanceof self) {
            throw new ContainerError(sprintf('%s instances cannot be stacked.', self::class));
        }

        if ($this->hasContainer($container)) {
            throw new ContainerError(sprintf('Cannot stack the same container (%s#%d) twice.', get_class($container), spl_object_id($container)));
        }

        if ($this->container) {
            $next = new static($this->container);
            $next->next = $this->next;
            $this->next = $next;
        }
        $this->container = $container;
    }

    /** {@inheritdoc} */
    public function get(string $id): mixed
    {
        try {
            return $this->container->get($id);
        } catch (ContainerExceptionInterface $prev) {
            if ($this->next) {
                return $this->next->get($id);
            }
            throw NotFound::for($id, $prev);
        }
    }

    /** {@inheritdoc} */
    public function has(string $id): bool
    {
        return $this->container->has($id) || ($this->next?->has($id) ?? false);
    }

}
