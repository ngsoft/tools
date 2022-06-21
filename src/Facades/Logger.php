<?php

declare(strict_types=1);

namespace NGSOFT\Facades;

use NGSOFT\Container\{
    ContainerInterface, ServiceProvider, SimpleServiceProvider
};
use Psr\Log\{
    LoggerInterface, NullLogger
};

class Logger extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return 'LoggerFacade';
    }

    protected static function getServiceProvider(): ServiceProvider
    {


        return new SimpleServiceProvider(self::getFacadeAccessor(),
                function (ContainerInterface $container) {

                    if ( ! $container->hasEntry(LoggerInterface::class)) {
                        $container->set(LoggerInterface::class, new NullLogger);
                    }
                    $logger = $container->get(LoggerInterface::class);

                    $container->set(self::getFacadeAccessor(), $logger);
                }
        );
    }

}
