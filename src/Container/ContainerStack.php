<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use LogicException;
use Psr\Container\{
    ContainerExceptionInterface, ContainerInterface
};
use Stringable,
    ValueError;

/**
 * A container stack that enables to use many DI solutions
 *
 */
class ContainerStack implements ContainerInterface, Stringable
{

    protected ?ContainerInterface $container = null;

    final public function __construct(
            ContainerInterface|array $container,
            protected bool $appendContainer = true,
            protected ?self $next = null
    )
    {
        if ($container === $next) {
            throw new LogicException(sprintf('Cannot stack the same container %s#%d on top of one another.', get_class($container), spl_object_id($container)));
        }

        if (is_array($container)) {
            foreach ($container as $containerInstance) {
                if ($containerInstance instanceof ContainerInterface === false) {
                    throw new ValueError(sprintf('Container does not implements %s', ContainerInterface::class));
                }

                if ($appendContainer) $this->appendContainer($containerInstance);
                else $this->prependContainer($containerInstance);
            }
        } else $this->container = $container;
    }

    /**
     * Adds a container to the stack
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function stackContainer(ContainerInterface $container): void
    {
        if ($this->appendContainer) $this->appendContainer($container);
        else $this->prependContainer($container);
    }

    /**
     * Add Container on the bottom of the stack
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function appendContainer(ContainerInterface $container): void
    {
        $this->assertContainerOnce($container);
        if (!$this->container) {
            $this->container = $container;
            return;
        }

        $this->next = new static($container, $this->appendContainer, $this->next);
    }

    /**
     * Add container on the top of the stack
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function prependContainer(ContainerInterface $container): void
    {
        $this->assertContainerOnce($container);
        if (!$this->container) {
            $this->container = $container;
            return;
        }
        $this->next = new static($this->container, $this->appendContainer, $this->next);
        $this->container = $container;
    }

    protected function assertContainerOnce(ContainerInterface $container)
    {

        if ($this->hasContainer($container)) {
            throw new ValueError('Cannot stack the same container twice.');
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

        if ($this->container === $container || $container === $this) {
            return true;
        }
        return $this->next?->hasContainer($container) ?? false;
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

    public function __debugInfo(): array
    {

        $result = [$this->__toString()];
        if ($this->next) $result[] = $this->next->__toString();

        return $result;
    }

    public function __toString()
    {
        return sprintf('object(%s)#%d', get_class($this->container), spl_object_id($this->container));
    }

}
