<?php

declare(strict_types=1);

namespace NGSOFT\Facades;

use NGSOFT\{
    Container\ServiceProvider, Lock\FileLock, Lock\LockServiceProvider
};

/**
 * @method static \NGSOFT\Lock\FileLock createFileLock(string $name, int $seconds = 0, string $owner = '', string $rootpath = '')
 * @method static \NGSOFT\Lock\SQLiteLock createSQLiteLock(string $name, int $seconds = 0, string $owner = '', string $dbname = 'sqlocks.db3', string $table = 'locks')
 * @method static \NGSOFT\Lock\NoLock createNoLock(string $name, int $seconds = 0, string $owner = '')
 * @method static \NGSOFT\Lock\CacheLock createCacheLock(\Psr\Cache\CacheItemPoolInterface $cache, string $name, int $seconds = 0, string $owner = '')
 * @method static \NGSOFT\Lock\SimpleCacheLock createSimpleCacheLock(\Psr\SimpleCache\CacheInterface $cache, string $name, int $seconds = 0, string $owner = '')
 * @see \NGSOFT\Lock\LockFactory
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
