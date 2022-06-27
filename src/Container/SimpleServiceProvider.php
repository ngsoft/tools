<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure;

class SimpleServiceProvider implements ServiceProvider
{

    protected array $provides = [];

    public function __construct(
            string|array $provides,
            protected mixed $register
    )
    {
        $this->provides = (array) $provides;
    }

    public function provides(): array
    {
        return array_values($this->provides);
    }

    public function register(ContainerInterface $container): void
    {
        $entry = $this->register;
        if (is_null($entry) || ! count($this->provides())) {
            return;
        }

        if ($entry instanceof Closure) {
            $entry($container);
            return;
        }

        $container->setMany(array_fill_keys($this->provides(), $entry));
    }

}
