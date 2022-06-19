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
        return ['Lock'];
    }

    public function register(ContainerInterface $container): void
    {

        $container->alias('Lock', LockFactory::class);

        $container->set(LockFactory::class, function (\NGSOFT\Container\Container $container) {
            $rootpath = '';
            if ($container->has('Lock.rootpath')) {
                $rootpath = $container->get(('locks.rootpath'));
            }
            $seconds = 0;
            if ($container->has('Lock.ttl')) {
                $seconds = $container->get(('locks.ttl'));
            }

            return new LockFactory($rootpath, $seconds);
        });
    }

}
