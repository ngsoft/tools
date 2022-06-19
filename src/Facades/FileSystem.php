<?php

declare(strict_types=1);

namespace NGSOFT\Facades;

use NGSOFT\{
    Container\NullServiceProvider, Container\ServiceProvider, Filesystem\FileFactory
};

class FileSystem extends Facade
{

    protected static function getServiceProvider(): ServiceProvider
    {

        return new NullServiceProvider;
    }

    protected static function getFacadeAccessor(): string
    {
        return FileFactory::class;
    }

}
