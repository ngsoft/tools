<?php

declare(strict_types=1);

namespace NGSOFT\Facades;

use NGSOFT\Container\{
    ServiceProvider, SimpleServiceProvider
};

class Session extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return 'SessionStorage';
    }

    protected static function getServiceProvider(): ServiceProvider
    {
        // please change this to declare custom services
        return new SimpleServiceProvider(static::getFacadeAccessor(), new \NGSOFT\Tools\SessionStorage);
    }

    /** {@inheritdoc} */
    public static function clear(): void
    {
        static::getFacadeRoot()->clear();
    }

    /** {@inheritdoc} */
    public static function getItem(string $key): mixed
    {
        return static::getFacadeRoot()->getItem($key);
    }

    /** {@inheritdoc} */
    public static function removeItem(string $key): void
    {
        static::getFacadeRoot()->removeItem($key);
    }

    /** {@inheritdoc} */
    public static function setItem(string $key, mixed $value): void
    {
        static::getFacadeRoot()->setItem($key, $value);
    }

    /** {@inheritdoc} */
    public static function hasItem(string $key): bool
    {
        return static::getFacadeRoot()->hasItem($key);
    }

}
