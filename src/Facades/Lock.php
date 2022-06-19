<?php

declare(strict_types=1);

namespace NGSOFT\Facades;

use NGSOFT\{
    Container\ServiceProvider, Lock\FileLock, Lock\LockServiceProvider
};

/**
 * @method static FileLock createFileLock(string $name, int $seconds = 0, string $owner = '', string $rootpath = '')
 */
class Lock extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return static::getAlias();
    }

    protected static function getServiceProvider(): ServiceProvider
    {
        return new LockServiceProvider();
    }

}
