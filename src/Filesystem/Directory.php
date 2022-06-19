<?php

declare(strict_types=1);

namespace NGSOFT\Filesystem;

use FilesystemIterator,
    InvalidArgumentException,
    RecursiveDirectoryIterator;
use function blank,
             mb_substr;
use function NGSOFT\Tools\{
    map, some
};
use function str_starts_with;

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

    protected function filesIterator(FileList $list, string|array $extensions = [], bool $hidden = false): iterable
    {

        if ( ! is_array($extensions)) {
            $extensions = empty($extensions) ? [] : [$extensions];
        }

        $extensions = map(fn($e) => str_starts_with($e, '.') ? mb_substr($e, 1) : $e, $extensions);

        $result = new FileList();

        foreach ($list as $file => $filesystem) {

            if ($filesystem instanceof File) {
                if ( ! blank($extensions)) {
                    if ( ! some(function ($ext) use ($filesystem) { return $ext === $filesystem->extension(); }, $extensions)) {
                        continue;
                    }
                }
                if ( ! $hidden && $filesystem->hidden()) {
                    continue;
                }

                $result->append($filesystem);
            }
        }

        return $result;
    }

    public function files(string|array $extensions = [], bool $hidden = false): iterable
    {
        yield from $this->filesIterator(self::scanFiles($this->path), $extensions, $hidden);
    }

    public function allFiles(string|array $extensions = '', bool $hidden = false): iterable
    {
        yield from $this->filesIterator(self::scanFiles($this->path, true), $extensions, $hidden);
    }

    public function directories(bool $hidden = false)
    {

    }

}
