<?php

declare(strict_types=1);

namespace NGSOFT\Facades;

use NGSOFT\{
    Container\ServiceProvider, Container\SimpleServiceProvider, Filesystem\Directory, Filesystem\File, Filesystem\FileContents, Filesystem\FileFactory
};

class FileSystem extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return FileFactory::class;
    }

    protected static function getServiceProvider(): ServiceProvider
    {
        return new SimpleServiceProvider(self::getFacadeAccessor(), new FileFactory());
    }

    /**
     * Get a File instance
     */
    public static function getFile(string $filename): File
    {
        return static::getFacadeRoot()->getFile($filename);
    }

    /**
     * Get a Directory instance
     */
    public static function getDirectory(string $dirname): Directory
    {
        return static::getFacadeRoot()->getDirectory($dirname);
    }

    /**
     * Get File Contents
     */
    public static function getFileContents(string $filename): FileContents
    {
        return static::getFacadeRoot()->getFileContents($filename);
    }

}
