<?php

declare(strict_types=1);

namespace NGSOFT\Filesystem;

use FilesystemIterator,
    InvalidArgumentException,
    IteratorAggregate,
    RecursiveDirectoryIterator,
    RuntimeException,
    Throwable,
    Traversable;
use function blank,
             mb_strlen,
             mb_substr;
use function NGSOFT\Tools\{
    map, some
};
use function str_ends_with,
             str_starts_with;

/**
 * Manages a directory
 */
class Directory extends Filesystem implements IteratorAggregate
{

    /** @var Directory[] */
    private static array $pushd = [];

    /**
     * Scan files in a directory
     * @param string $dirname
     * @param bool $recursive
     * @return FileList
     */
    public static function scanFiles(string $dirname, bool $recursive = false): FileList
    {

        static $ignore = ['.', '..'];

        $result = new FileList();

        if ( ! is_dir($dirname)) {
            return $result;
        }
        $dirs = [];

        foreach (scandir($dirname) as $file) {
            if (in_array($file, $ignore)) {
                continue;
            }
            $path = $dirname . DIRECTORY_SEPARATOR . $file;

            if ( ! $recursive || ! is_dir($path)) {
                $result->append($path);
                continue;
            }

            if (is_dir($path)) {
                $dirs[] = $path;
            }
        }


        foreach ($dirs as $dir) {
            $result->append(static::scanFiles($dir, $recursive));
        }

        return $result;
    }

    public static function scanFilesArray(string $dirname, bool $recursive = false): array
    {
        return self::scanFiles($dirname, $recursive)->keys();
    }

    public static function cwd(): static
    {
        return new static(realpath(getcwd()));
    }

    public static function pushd(string|self $directory): static
    {
        if (is_string($directory)) {
            $directory = new static($directory);
        }
        /** @var self $last */
        if ($last = end(self::$pushd)) {
            if ($last->getRealpath() === $directory->getRealpath()) {
                return $last;
            }
        }
    }

    public static function popd()
    {

    }

    public function __construct(
            protected string $path
    )
    {
        if (is_file($path)) {
            throw new InvalidArgumentException(sprintf('%s is a regular file.', $path));
        }
        parent::__construct($path);
    }

    protected function copyDir(string $directory, string $destination): bool
    {

        if ( ! is_dir($directory)) {
            return false;
        }

        $directory = realpath($directory);
        $destination = normalize_path($destination);

        if ( ! file_exists($destination)) {
            $this->createDir($destination);
        } elseif ( ! is_dir($destination)) {
            throw new RuntimeException(sprintf('Cannot copy files to %s, not a directory.', $destination));
        }

        if (is_dir($destination)) {
            $destination = realpath($destination);
        }



        $len = mb_strlen($directory);

        /** @var File $type */
        foreach (self::scanFiles($directory, true) as $file => $type) {
            if ($relative = mb_substr($file, $len)) {
                $path = $destination . $relative;

                if ($path === $file) {
                    return false;
                }
                try {
                    $type->copy($path, $success);
                    if ($success === false) {
                        return false;
                    }
                } catch (Throwable) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Copy Directory to another location
     *
     * @param string $target new file
     * @param ?bool $success True if the operation succeeded
     * @return static a Directory instance for the target
     */
    public function copy(string $target, bool &$success = null): static
    {

        $success = false;
        if ($this->exists()) {
            $success = $this->copyDir($this->path, $target);
        }

        return new static($target);
    }

    /**
     * Checks if directory exists
     * @return bool
     */
    public function exists(): bool
    {
        return is_dir($this->path);
    }

    /**
     * Checks if no files
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        if ($this->exists()) {
            return true;
        }

        $iterator = new RecursiveDirectoryIterator($this->path, FilesystemIterator::SKIP_DOTS);
        return iterator_count($iterator) === 0;
    }

    /**
     * Create dir
     *
     * @param int $mode
     * @param bool $recursive
     * @return bool
     */
    public function mkdir(int $mode = 0777, bool $recursive = true): bool
    {
        return $this->exists() || mkdir($this->path, $mode, $recursive);
    }

    /**
     * Remove dir
     *
     * @return bool
     */
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

    public function search(string $query, bool $fileOnly = true): FileList
    {

    }

    /**
     * List files inside directory
     *
     * @param string|array $extensions
     * @param bool $hidden
     * @return FileList
     */
    public function files(string|array $extensions = [], bool $hidden = false): FileList
    {
        return $this->filesIterator(self::scanFiles($this->path), $extensions, $hidden);
    }

    /**
     * List files recursively
     * @param string|array $extensions
     * @param bool $hidden
     * @return FileList
     */
    public function allFiles(string|array $extensions = [], bool $hidden = false): FileList
    {
        return $this->filesIterator(self::scanFiles($this->path, true), $extensions, $hidden);
    }

    /**
     * List directories
     *
     * @return FileList
     */
    public function directories(bool $recursive = false): FileList
    {

        $list = self::scanFiles($this->path);

        if ($recursive) {
            $result = new FileList();

            foreach ($list as $dir) {

                if ($dir instanceof Directory) {
                    $sub = $dir->directories(true);
                    if (count($sub) > 0) {
                        $result->append($sub);
                    } else { $result->append($dir); }
                }
            }

            return $result;
        }
        return $list->filter(fn($f) => $f instanceof Directory);
    }

    /**
     * Access a file in that directory
     *
     * @param string $target
     * @return File|Directory
     */
    public function getFile(string $target): File|Directory
    {

        $path = normalize_path($this->path . DIRECTORY_SEPARATOR . $target);
        if (is_dir($path)) {
            return self::create($path);
        }
        return File::create($path);
    }

    public function getIterator(): Traversable
    {
        yield from self::scanFiles($this->path);
    }

}
