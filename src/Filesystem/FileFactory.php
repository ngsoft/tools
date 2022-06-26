<?php

declare(strict_types=1);

namespace NGSOFT\Filesystem;

class FileFactory
{

    /**
     * Get a File instance
     */
    public function getFile(string $filename): File
    {
        return File::create($filename);
    }

    /**
     * Get a Directory instance
     */
    public function getDirectory(string $dirname): Directory
    {
        return Directory::create($dirname);
    }

    /**
     * Get File Contents
     */
    public function getFileContents(string $filename): FileContents
    {
        return $this->getFile($filename)->getContents();
    }

}
