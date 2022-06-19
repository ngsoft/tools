<?php

declare(strict_types=1);

namespace NGSOFT\Container;

class NullServiceProvider implements ServiceProvider
{

    public function provides(): array
    {
        return [];
    }

    public function register(ContainerInterface $container): void
    {

    }

}
