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

                    if ( ! $container->has(LoggerInterface::class)) {
                        $container->set(LoggerInterface::class, new NullLogger);
                    }
                    $logger = $container->get(LoggerInterface::class);

                    $container->set(self::getFacadeAccessor(), $logger);
                }
        );
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string|\Stringable $message
     * @param array $context
     *
     * @return void
     *
     * @throws \Psr\Log\InvalidArgumentException
     */
    public static function log(mixed $level, \Stringable|string $message, array $context = []): void
    {
        static::getFacadeRoot()->log($level, $message, $context);
    }

    /**
     * System is unusable.
     *
     * @param string|\Stringable $message
     * @param array  $context
     *
     * @return void
     */
    public static function emergency(\Stringable|string $message, array $context = []): void
    {
        static::getFacadeRoot()->emergency($message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string|\Stringable $message
     * @param array  $context
     *
     * @return void
     */
    public static function alert(\Stringable|string $message, array $context = []): void
    {
        static::getFacadeRoot()->alert($message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string|\Stringable $message
     * @param array  $context
     *
     * @return void
     */
    public static function critical(\Stringable|string $message, array $context = []): void
    {
        static::getFacadeRoot()->critical($message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string|\Stringable $message
     * @param array  $context
     *
     * @return void
     */
    public static function error(\Stringable|string $message, array $context = []): void
    {
        static::getFacadeRoot()->error($message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string|\Stringable $message
     * @param array  $context
     *
     * @return void
     */
    public static function warning(\Stringable|string $message, array $context = []): void
    {
        static::getFacadeRoot()->warning($message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string|\Stringable $message
     * @param array  $context
     *
     * @return void
     */
    public static function notice(\Stringable|string $message, array $context = []): void
    {
        static::getFacadeRoot()->notice($message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string|\Stringable $message
     * @param array  $context
     *
     * @return void
     */
    public static function info(\Stringable|string $message, array $context = []): void
    {
        static::getFacadeRoot()->info($message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string|\Stringable $message
     * @param array  $context
     *
     * @return void
     */
    public static function debug(\Stringable|string $message, array $context = []): void
    {
        static::getFacadeRoot()->debug($message, $context);
    }

}
