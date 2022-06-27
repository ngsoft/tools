<?php

declare(strict_types=1);

namespace NGSOFT\Lock;

use NGSOFT\Filesystem\File;
use Psr\{
    Cache\CacheItemPoolInterface, SimpleCache\CacheInterface
};

class LockFactory
{

    public function __construct(
            protected $rootpath = '',
            protected int|float $seconds = 0,
            protected string $owner = ''
    )
    {
        if (empty($rootpath)) {
            $this->rootpath = sys_get_temp_dir();
        }
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
    public function createFileLock(string $name, int $seconds = 0, string $owner = '', string $rootpath = ''): FileLock
    {
        if (empty($rootpath)) {
            $rootpath = $this->rootpath;
        }
        if ($seconds === 0) {
            $seconds = $this->seconds;
        }
        return new FileLock($name, $seconds, $owner, rootpath: $rootpath);
    }

    /**
     *
     * @param string|File $file
     * @param int $seconds
     * @param string $owner
     * @return FileSystemLock
     */
    public function createFileSystemLock(string|File $file, int $seconds, string $owner = ''): FileSystemLock
    {
        if ($seconds === 0) {
            $seconds = $this->seconds;
        }

        return new FileSystemLock($file instanceof File ? $file : File::create($file), $seconds, $owner);
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
    public function createSQLiteLock(string $name, int $seconds = 0, string $owner = '', string $dbname = 'sqlocks.db3', string $table = 'locks'): SQLiteLock
    {
        $db = $this->rootpath . DIRECTORY_SEPARATOR . $dbname;
        if ($seconds === 0) {
            $seconds = $this->seconds;
        }

        return new SQLiteLock($name, $seconds, $db, $owner, table: $table);
    }

    /**
     * Create a NoLock
     *
     * @param string $name
     * @param int $seconds
     * @param string $owner
     * @return NoLock
     */
    public function createNoLock(string $name, int $seconds = 0, string $owner = ''): NoLock
    {
        if ($seconds === 0) {
            $seconds = $this->seconds;
        }
        return new NoLock($name, $seconds, $owner);
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
    public function createCacheLock(CacheItemPoolInterface $cache, string $name, int $seconds = 0, string $owner = ''): CacheLock
    {
        if ($seconds === 0) {
            $seconds = $this->seconds;
        }
        return new CacheLock($cache, $name, $seconds, $owner);
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
    public function createSimpleCacheLock(CacheInterface $cache, string $name, int $seconds = 0, string $owner = ''): SimpleCacheLock
    {
        if ($seconds === 0) {
            $seconds = $this->seconds;
        }

        return new SimpleCacheLock($cache, $name, $seconds, $owner);
    }

}
