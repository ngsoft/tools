<?php

declare(strict_types=1);

namespace NGSOFT\Facades;

use Generator;
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

        return new SimpleServiceProvider($accessor,
                static function (ContainerInterface $container) use ($accessor) {
                    $instance = new WatchFactory();
                    $instance->start('global', SCRIPT_START);
                    $container->set($accessor, $instance);
                }
        );
    }

    /**
     * Get a watch
     */
    public static function getWatch(mixed $task = WatchFactory::DEFAULT_WATCH): StopWatch
    {
        return static::getFacadeRoot()->getWatch($task);
    }

    /**
     * Reads the clock
     */
    public static function read(mixed $task = WatchFactory::DEFAULT_WATCH): StopWatchResult
    {
        return static::getFacadeRoot()->read($task);
    }

    /**
     * Starts the clock
     */
    public static function start(mixed $task = WatchFactory::DEFAULT_WATCH, int|float|null $startTime = null): bool
    {
        return static::getFacadeRoot()->start($task, $startTime);
    }

    /**
     * Resumes the clock (only if paused)
     *
     * @return bool
     */
    public static function resume(mixed $task = WatchFactory::DEFAULT_WATCH): bool
    {
        return static::getFacadeRoot()->resume($task);
    }

    /**
     * Resets the clock
     */
    public static function reset(mixed $task = WatchFactory::DEFAULT_WATCH): void
    {
        static::getFacadeRoot()->reset($task);
    }

    /**
     * Resets all the clocks
     */
    public static function resetAll(): void
    {
        static::getFacadeRoot()->resetAll();
    }

    /**
     * Pauses the clock
     */
    public static function pause(mixed $task = WatchFactory::DEFAULT_WATCH, ?bool &$success = null): StopWatchResult
    {
        return static::getFacadeRoot()->pause($task, $success);
    }

    /**
     * Stops the clock
     */
    public static function stop(mixed $task = WatchFactory::DEFAULT_WATCH, ?bool &$success = null): StopWatchResult
    {
        return static::getFacadeRoot()->stop($task, $success);
    }

    /**
     * @return Generator|StopWatchResult[]
     */
    public static function getLaps(mixed $task = WatchFactory::DEFAULT_WATCH): iterable
    {
        return static::getFacadeRoot()->getLaps($task);
    }

    /**
     * Adds a lap time
     */
    public static function lap(mixed $task = WatchFactory::DEFAULT_WATCH, ?string $label = null): bool
    {
        return static::getFacadeRoot()->lap($task, $label);
    }

}
