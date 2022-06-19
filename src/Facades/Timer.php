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

    public const GLOBAL_CLOCK = 'global';

    protected static function getFacadeAccessor(): string
    {
        return static::getAlias();
    }

    protected static function getServiceProvider(): ServiceProvider
    {
        return new SimpleServiceProvider(self::getAlias(),
                static function (ContainerInterface $container) {


                    $hrtime = false;
                    if ($container->has('Timer.hrtime')) {
                        $hrtime = $container->get('Timer.hrtime');
                    }

                    $instance = new WatchFactory($hrtime);
                    $instance->start('global', SCRIPT_START);

                    $container->set('Timer', $instance);
                }
        );
    }

}
