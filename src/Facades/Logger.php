<?php

declare(strict_types=1);

namespace NGSOFT\Facades;

use NGSOFT\Container\{
    ContainerInterface, ServiceProvider, SimpleServiceProvider
};
use Psr\Log\{
    LoggerInterface, NullLogger
};

/**
 * @method static void log(mixed $level, \Stringable|string $message, array $context = [])
 * @method static void emergency(\Stringable|string $message, array $context = [])
 * @method static void alert(\Stringable|string $message, array $context = [])
 * @method static void critical(\Stringable|string $message, array $context = [])
 * @method static void error(\Stringable|string $message, array $context = [])
 * @method static void warning(\Stringable|string $message, array $context = [])
 * @method static void notice(\Stringable|string $message, array $context = [])
 * @method static void info(\Stringable|string $message, array $context = [])
 * @method static void debug(\Stringable|string $message, array $context = [])
 * @see \Psr\Log\NullLogger
 */
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
