<?php

declare(strict_types=1);

namespace NGSOFT\Facades;

use NGSOFT\Container\{
    ContainerInterface, ServiceProvider
};

/**
 * @method static bool has(string $id)
 * @method static \NGSOFT\Container\Container addResolutionHandler(\Closure|\NGSOFT\Container\ContainerResolver $handler)
 * @method static \NGSOFT\Container\Container register(\NGSOFT\Container\ServiceProvider $provider)
 * @method static mixed get(string $id)
 * @method static void setMultiple(array $definitions)
 * @method static void set(string $id, mixed $entry)
 * @method static \NGSOFT\Container\Container alias(string $alias, string $id)
 * @method static \NGSOFT\Container\Container extend(string $id, \Closure $closure)
 * @see \NGSOFT\Container\Container
 */
class Container extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return static::getAlias();
    }

}
