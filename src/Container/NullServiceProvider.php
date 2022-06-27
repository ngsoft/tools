<?php

declare(strict_types=1);

namespace NGSOFT\Container;

/** @phan-file-suppress PhanUnusedPublicMethodParameter */
class NullServiceProvider implements ServiceProvider
{

    protected array $provides = [];

    public function __construct(string|array $provides = [])
    {

        $this->provides = array_values((array) $provides);
    }

    public function provides(): array
    {
        return $this->provides;
    }

    public function register(ContainerInterface $container): void
    {

    }

}
