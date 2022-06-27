<?php

declare(strict_types=1);

namespace NGSOFT\Facades;

use NGSOFT\Container\{
    ContainerInterface, ServiceProvider
};

class Container extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return static::getAlias();
    }

}
