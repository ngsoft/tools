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
        ['locks'];
    }

    public function register(ContainerInterface $container): void
    {


        $container->set(LockFactory::class, function (\NGSOFT\Container\Container $container) {
            $container->alias('locks', LockFactory::class);
            $rootpath = '';
            if ($container->has('locks.rootpath')) {
                $rootpath = $container->get(('locks.rootpath'));
            }
            $seconds = 0;
            if ($container->has('locks.ttl')) {
                $seconds = $container->get(('locks.ttl'));
            }

            return new LockFactory($rootpath, $seconds);
        });
    }

}
