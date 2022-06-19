<?php

declare(strict_types=1);

namespace NGSOFT\Filesystem;

class FileFactory
{

    public function getFile(string $filename): File
    {
        return File::create($filename);
    }

    public function getDirectory(string $dirname): Directory
    {
        return Directory::create($dirname);
    }

}
