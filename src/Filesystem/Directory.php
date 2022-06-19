<?php

declare(strict_types=1);

namespace NGSOFT\Filesystem;

class Directory extends Filesystem
{

    public function __construct(
            protected string $path
    )
    {
        if (is_file($path)) {
            throw new InvalidArgumentException(sprintf('%s is a regular file.', $path));
        }
        parent::__construct($path);
    }

    public function exists(): bool
    {
        return is_dir($this->path);
    }

    public function isEmpty(): bool
    {
        $iterator = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
        return iterator_count($iterator) === 0;
    }

    public function mkdir(int $mode = 0777, bool $recursive = true): bool
    {
        return $this->exists() || mkdir($this->path, $mode, $recursive);
    }

    public function rmdir(): bool
    {
        if ( ! $this->exists()) {
            return true;
        }

        return $this->isEmpty() && $this->rmdir($this->path);
    }

    public static function scanFiles(string $dirname, bool $recursive = false): iterable
    {



        static $ignore = ['.', '..'];
        if ( ! is_dir($dirname)) {
            return;
        }

        $files = $dirs = [];

        foreach (scandir($dirname) as $file) {
            if (in_array($file, $ignore)) {
                continue;
            }
            $path = $dirname . DIRECTORY_SEPARATOR . $file;

            if ( ! $recursive || ! is_dir($path)) {

                yield $path;

                continue;
            }

            if (is_dir($path)) {
                $dirs[] = $path;
            }
        }


        foreach ($dirs as $dir) {
            yield from static::scanFiles($dir, $recursive);
        }
    }

    public static function scanFilesArray(string $dirname, bool $recursive = false): array
    {

        $result = [];

        return $result;
    }

    public function files(string|array $extensions = '', bool $hidden = false): iterable
    {

    }

    public function allFiles(string|array $extensions = '', bool $hidden = false): iterable
    {

    }

    public function directories(bool $hidden = false)
    {

    }

}
