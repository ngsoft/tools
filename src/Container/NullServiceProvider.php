<?php

declare(strict_types=1);

namespace NGSOFT\Container;

/** @phan-file-suppress PhanUnusedPublicMethodParameter */
class NullServiceProvider implements ServiceProvider
{

    public function __construct(protected string|array $provides = [])
    {

        $this->provides = array_values(is_string($provides) ? [$provides] : $provides);
    }

    public function provides(): array
    {
        return [];
    }

    public function register(ContainerInterface $container): void
    {

    }

}
