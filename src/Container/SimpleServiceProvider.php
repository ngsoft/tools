<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure;

class SimpleServiceProvider implements ServiceProvider
{

    protected array $provides = [];

    public function __construct(
            string|iterable $provides,
            protected Closure $register
    )
    {
        if ( ! is_iterable($provides)) {
            $provides = [];
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
        $closure($container);
    }

}
