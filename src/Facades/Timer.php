<?php

declare(strict_types=1);

namespace NGSOFT\Facades;

use NGSOFT\{
    Container\ContainerInterface, Container\ServiceProvider, Timer\WatchFactory
};

/**
 * @method static \NGSOFT\Timer\StopWatch getWatch(mixed $task = 'default')
 * @method static \NGSOFT\Timer\StopWatchResult read(mixed $task = 'default')
 * @method static bool start(mixed $task = 'default', int|float|null $startTime = NULL)
 * @method static bool resume(mixed $task = 'default')
 * @method static void reset(mixed $task = 'default')
 * @method static void resetAll()
 * @method static \NGSOFT\Timer\StopWatchResult pause(mixed $task = 'default', ?bool $success = NULL)
 * @method static \NGSOFT\Timer\StopWatchResult stop(mixed $task = 'default', ?bool $success = NULL)
 * @method static iterable getLaps(mixed $task = 'default')
 * @method static bool lap(mixed $task = 'default', ?string $label = NULL)
 * @see \NGSOFT\Timer\WatchFactory
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
        return new class implements ServiceProvider{

            public function provides(): array
            {
                return ['Timer'];
            }

            public function register(ContainerInterface $container): void
            {
                $container->alias('Timer', WatchFactory::class);

                $container->set(WatchFactory::class, function (ContainerInterface $container) {

                    $hrtime = false;
                    if ($container->has('Timer.hrtime')) {
                        $hrtime = $container->get('Timer.hrtime');
                    }

                    $instance = new WatchFactory($hrtime);
                    $instance->start('global', SCRIPT_START);
                    return $instance;
                });
            }
        };
    }

}
