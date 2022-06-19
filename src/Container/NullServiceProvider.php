<?php

declare(strict_types=1);

namespace NGSOFT\Container;

/** @phan-file-suppress PhanUnusedPublicMethodParameter */
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
