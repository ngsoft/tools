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

        if ($container->hasEntry('Config')) {
            $rootpath = $container->get('Config')['lock.rootpath'] ?? $rootpath;
            $seconds = $container->get('Config')['lock.seconds'] ?? $seconds;
        }

        $container->setMultiple(array_fill_keys([], new LockFactory($rootpath, $seconds)));
    }

}
