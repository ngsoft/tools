<?php

declare(strict_types=1);

namespace NGSOFT\Filesystem;

use FilesystemIterator,
    InvalidArgumentException,
    RecursiveDirectoryIterator;
use function blank;
use function NGSOFT\Tools\{
    map, some
};
use function str_ends_with,
             str_starts_with;

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
        if ($this->exists()) {
            return true;
        }

        $iterator = new RecursiveDirectoryIterator($this->path, FilesystemIterator::SKIP_DOTS);
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

        return $this->isEmpty() && rmdir($this->path);
    }

    protected function filesIterator(FileList $list, string|array $extensions = [], bool $hidden = false): FileList
    {

        if ( ! is_array($extensions)) {
            $extensions = empty($extensions) ? [] : [$extensions];
        }

        $extensions = map(fn($e) => ! str_starts_with($e, '.') ? ".$e" : $e, $extensions);

        return $list->filter(
                        function (Filesystem $filesystem, string $file) use ($extensions, $hidden) {
                            if ( ! $filesystem instanceof File) {
                                return false;
                            }

                            if (
                                    ! blank($extensions) &&
                                    ! some(function ($ext) use ($file) { return str_ends_with($file, $ext); }, $extensions)
                            ) {
                                return false;
                            }
                            if ( ! $hidden && $filesystem->hidden()) {
                                return false;
                            }

                            return true;
                        }
        );
    }

    public function files(string|array $extensions = [], bool $hidden = false): FileList
    {
        return $this->filesIterator(self::scanFiles($this->path), $extensions, $hidden);
    }

    public function allFiles(string|array $extensions = '', bool $hidden = false): FileList
    {
        return $this->filesIterator(self::scanFiles($this->path, true), $extensions, $hidden);
    }

    public function directories(): FileList
    {
        return self::scanFiles($this->path)->filter(fn($f) => $f instanceof Directory);
    }

}
