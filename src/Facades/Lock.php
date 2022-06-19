<?php

declare(strict_types=1);

namespace NGSOFT\Facades;

class Lock extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return 'locks';
    }

    protected static function getServiceProvider(): \NGSOFT\Container\ServiceProvider
    {

    }

}
