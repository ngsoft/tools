<?php

declare(strict_types=1);

namespace NGSOFT\Facades;

use NGSOFT\{
    Container\ServiceProvider, Container\SimpleServiceProvider, Filesystem\FileFactory
};

/**
 * @method static \NGSOFT\Filesystem\File getFile(string $filename)
 * @method static \NGSOFT\Filesystem\Directory getDirectory(string $dirname)
 * @see \NGSOFT\Filesystem\FileFactory
 */
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

}
