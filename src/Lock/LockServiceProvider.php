<?php

declare(strict_types=1);

namespace NGSOFT\Lock;

use NGSOFT\Container\{
    ContainerInterface, ServiceProvider
};

class LockServiceProvider implements ServiceProvider
{

    public function provides(): array
    {
        return ['Lock', LockFactory::class];
    }

    public function register(ContainerInterface $container): void
    {

        $rootpath = '';
        $seconds = 0;
        if ($container->has('Config')) {
            $rootpath = $container->get('Config')['lock.rootpath'] ?? $rootpath;
            $seconds = $container->get('Config')['lock.seconds'] ?? $seconds;
        }

        $container->setMany(array_fill_keys($this->provides(), new LockFactory($rootpath, $seconds)));
    }

}
