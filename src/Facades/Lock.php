<?php

declare(strict_types=1);

namespace NGSOFT\Facades;

use NGSOFT\{
    Container\ServiceProvider, Filesystem\File, Lock\CacheLock, Lock\FileLock, Lock\FileSystemLock, Lock\LockServiceProvider, Lock\NoLock, Lock\SimpleCacheLock,
    Lock\SQLiteLock
};
use Psr\{
    Cache\CacheItemPoolInterface, SimpleCache\CacheInterface
};

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

    /**
     * Create a Php File Lock
     *
     * @param string $name
     * @param int $seconds
     * @param string $owner
     * @param string $rootpath
     * @return FileLock
     */
    public static function createFileLock(string $name, int $seconds = 0, string $owner = '', string $rootpath = ''): FileLock
    {
        return static::getFacadeRoot()->createFileLock($name, $seconds, $owner, $rootpath);
    }

    /**
     *
     * @param string|File $file
     * @param int $seconds
     * @param string $owner
     * @return FileSystemLock
     */
    public static function createFileSystemLock(File|string $file, int $seconds, string $owner = ''): FileSystemLock
    {
        return static::getFacadeRoot()->createFileSystemLock($file, $seconds, $owner);
    }

    /**
     * Create a SQLite Lock
     *
     * @param string $name
     * @param int $seconds
     * @param string $owner
     * @param string $dbname
     * @param string $table
     * @return SQLiteLock
     */
    public static function createSQLiteLock(string $name, int $seconds = 0, string $owner = '', string $dbname = 'sqlocks.db3', string $table = 'locks'): SQLiteLock
    {
        return static::getFacadeRoot()->createSQLiteLock($name, $seconds, $owner, $dbname, $table);
    }

    /**
     * Create a NoLock
     *
     * @param string $name
     * @param int $seconds
     * @param string $owner
     * @return NoLock
     */
    public static function createNoLock(string $name, int $seconds = 0, string $owner = ''): NoLock
    {
        return static::getFacadeRoot()->createNoLock($name, $seconds, $owner);
    }

    /**
     * Create a lock using a PSR-6 Cache
     *
     * @param CacheItemPoolInterface $cache
     * @param string $name
     * @param int $seconds
     * @param string $owner
     * @return CacheLock
     */
    public static function createCacheLock(CacheItemPoolInterface $cache, string $name, int $seconds = 0, string $owner = ''): CacheLock
    {
        return static::getFacadeRoot()->createCacheLock($cache, $name, $seconds, $owner);
    }

    /**
     * Create a lock using a PSR-16 Cache
     *
     * @param CacheInterface $cache
     * @param string $name
     * @param int $seconds
     * @param string $owner
     * @return SimpleCacheLock
     */
    public static function createSimpleCacheLock(CacheInterface $cache, string $name, int $seconds = 0, string $owner = ''): SimpleCacheLock
    {
        return static::getFacadeRoot()->createSimpleCacheLock($cache, $name, $seconds, $owner);
    }

}
