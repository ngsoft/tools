<?php

declare(strict_types=1);

namespace NGSOFT\Facades;

use NGSOFT\{
    Container\ContainerInterface, Container\ServiceProvider, Container\SimpleServiceProvider, Timer\StopWatch, Timer\StopWatchResult, Timer\WatchFactory
};
use const SCRIPT_START;

/**
 * @method static StopWatch getWatch(mixed $task = 'default')
 * @method static StopWatchResult read(mixed $task = 'default')
 * @method static bool start(mixed $task = 'default', int|float|null $startTime = NULL)
 * @method static bool resume(mixed $task = 'default')
 * @method static void reset(mixed $task = 'default')
 * @method static void resetAll()
 * @method static StopWatchResult pause(mixed $task = 'default', ?bool $success = NULL)
 * @method static StopWatchResult stop(mixed $task = 'default', ?bool $success = NULL)
 * @method static iterable getLaps(mixed $task = 'default')
 * @method static bool lap(mixed $task = 'default', ?string $label = NULL)
 * @see WatchFactory
 */
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
