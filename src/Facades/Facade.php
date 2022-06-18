<?php

declare(strict_types=1);

namespace NGSOFT\Facades;

abstract class Facade
{

    protected static \Psr\Container\ContainerInterface $container;

    public static function __callStatic(string $name, array $arguments): mixed
    {

    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    abstract protected static function getFacadeAccessor(): string;

    public static function swap(mixed $instance): void
    {

    }

    public static function getFacadeRoot(): mixed
    {

    }

    protected static function getAlias(): string
    {
        return class_basename(static::class);
    }

    public static function getContainer(): \Psr\Container\ContainerInterface
    {
        return self::$container;
    }

    public static function setContainer(\Psr\Container\ContainerInterface $container): void
    {
        self::$container = $container;
    }

}
