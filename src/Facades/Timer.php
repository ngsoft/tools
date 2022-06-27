<?php

declare(strict_types=1);

namespace NGSOFT\Facades;

use NGSOFT\{
    Container\ContainerInterface, Container\ServiceProvider, Container\SimpleServiceProvider, Timer\StopWatch, Timer\StopWatchResult, Timer\WatchFactory
};
use const SCRIPT_START;

class Timer extends Facade
{

    public const GLOBAL_TIMER = 'global';

    protected static function getFacadeAccessor(): string
    {
        return static::getAlias();
    }

    protected static function getServiceProvider(): ServiceProvider
    {

        $accessor = static::getFacadeAccessor();

        return new SimpleServiceProvider(self::getFacadeAccessor(),
                static function (ContainerInterface $container) use ($accessor) {
                    $instance = new WatchFactory();
                    $instance->start('global', SCRIPT_START);
                    $container->set($accessor, $instance);
                }
        );
    }

}
