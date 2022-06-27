<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use LogicException,
    NGSOFT\Traits\StringableObject;
use Psr\Container\{
    ContainerExceptionInterface, ContainerInterface
};
use Stringable,
    ValueError;

/**
 * A container stack that enables the use many DI solutions simultaneously
 * the first match in the stack will be returned
 */
class StackedContainer implements ContainerInterface, Stringable
{

    use StringableObject;

    protected ?ContainerInterface $container = null;

    /**
     *
     * @param ContainerInterface|ContainerInterface[] $container A single or multiple container(s) to manage
     * @param bool $appendContainer  append or prepend container using stackContainer method
     * @param self|null $next next container in the stack
     * @throws LogicException
     * @throws ValueError
     */
    final public function __construct(
            ContainerInterface|array $container,
            protected bool $appendContainer = true,
            protected ?self $next = null
    )
    {

        if (is_array($container)) {
            foreach ($container as $containerInstance) {
                $this->stackContainer($containerInstance);
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
        if ($this->appendContainer) {
            $this->appendContainer($container);
        } else {
            $this->prependContainer($container);
        }
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
        if ( ! $this->container) {
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
        if ( ! $this->container) {
            $this->container = $container;
            return;
        }
        $this->next = new static($this->container, $this->appendContainer, $this->next);
        $this->container = $container;
    }

    protected function assertContainerOnce(ContainerInterface $container)
    {

        if ($container instanceof self) {
            throw new LogicException('Cannot use a stack as a container.');
        }

        if ($this->hasContainer($container)) {
            throw new LogicException('Cannot stack the same container twice.');
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
            if ($this->next) { return $this->next->get($id); }
            throw $error;
        }
    }

    /** {@inheritdoc} */
    public function has(string $id): bool
    {
        return $this->container->has($id) || ($this->next?->has($id) ?? false);
    }

    /** {@inheritdoc} */
    public function __debugInfo(): array
    {
        $result = [$this->__toString()];
        if ($this->next) { $result[] = $this->next->__toString(); }
        return $result;
    }

}
