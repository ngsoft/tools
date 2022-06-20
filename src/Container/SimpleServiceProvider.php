<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure;

class SimpleServiceProvider implements ServiceProvider
{

    protected array $provides = [];

    /**
     *
     * @param string|iterable $provides
     * @param mixed|Closure $register
     */
    public function __construct(
            string|iterable $provides,
            protected mixed $register
    )
    {
        if ( ! is_iterable($provides)) {
            $provides = [$provides];
        }

        foreach ($provides as $id) {
            $this->provides[$id] = $id;
        }
    }

    public function provides(): array
    {
        return array_values($this->provides);
    }

    public function register(ContainerInterface $container): void
    {
        $closure = $this->register;

        if ($closure instanceof Closure) {
            $closure($container);
            return;
        }

        foreach ($this->provides as $id) {
            $container->set($id, $this->register);
        }
    }

}
