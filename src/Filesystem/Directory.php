<?php

declare(strict_types=1);

namespace NGSOFT\Filesystem;

use FilesystemIterator,
    InvalidArgumentException,
    IteratorAggregate,
    NGSOFT\Tools,
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
use function preg_valid,
             str_ends_with,
             str_starts_with;

/**
 * Manages a directory
 */
class Directory extends Filesystem implements IteratorAggregate
{

    protected const GLOB_SEARCH = '*?[]';

    /** @var Directory[] */
    protected static array $pushd = [];

    /**
     * Scan files inside directory
     */
    protected static function scan(string $dirname, bool $recursive): array
    {

        static $ignore = ['.', '..'];
        $result = [];

        if ( ! is_dir($dirname)) {
            return $result;
        }
        $len = mb_strlen($dirname) + 1;

        foreach (scandir($dirname) as $file) {
            if (in_array($file, $ignore)) {
                continue;
            }
            $absolute = $dirname . DIRECTORY_SEPARATOR . $file;

            if ( ! $recursive || ! is_dir($absolute)) {
                $result[$absolute] = $absolute;
                continue;
            }

            if (is_dir($absolute)) {
                $result += self::scan($absolute, $recursive);
            }
        }

        return map(function (string $absolute, &$relative) use ($len) {
            $relative = mb_substr($absolute, $len);
            return $absolute;
        }, $result);
    }

    /**
     * Scan files in a directory
     * @param string $dirname
     * @param bool $recursive
     * @return FileList
     */
    public static function scanFiles(string $dirname, bool $recursive = false): FileList
    {
        return FileList::create(self::scan($dirname, $recursive));
    }

    public static function scanFilesArray(string $dirname, bool $recursive = false): array
    {
        return static::scanFiles($dirname, $recursive)->toArray();
    }

    public static function cwd(): static
    {
        return new static(getcwd());
    }

    /**
     * Change the current active directory and stores the last position
     */
    public static function pushd(string|self $directory): static|false
    {
        if (is_string($directory)) {
            $directory = new static($directory);
        }

        if ( ! $directory->exists()) {
            return false;
        }

        if ($directory->chdir()) {
            static::$pushd[] = $directory;
            return $directory;
        }
        return false;
    }

    /**
     * Restore the last active directory position and returns it
     */
    public static function popd(): static|false
    {
        if ($previous = array_pop(static::$pushd)) {
            return $previous->chdir() ? $previous : false;
        }

        return false;
    }

    public function __construct(
            protected string $path
    )
    {

        if (blank($path)) {
            $path = getcwd();
        }

        if (is_file(static::getAbsolute($path))) {
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
        foreach (static::scanFiles($directory, true) as $file => $type) {
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
        $real = $this->realpath();
        return $real && is_dir($real);
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
     */
    public function rmdir(): bool
    {
        if ( ! $this->exists()) {
            return true;
        }

        return $this->isEmpty() && rmdir($this->path);
    }

    /**
     * Change dir
     */
    public function chdir(): bool
    {
        if ( ! $this->exists()) {
            return false;
        }

        return $this->isCurrentWorkingDir() || chdir($this->path);
    }

    /**
     * Checks if is current active dir
     */
    public function isCurrentWorkingDir(): bool
    {
        return getcwd() === $this->realpath();
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

    /**
     * Search for a file recursively using regex, glob or check if filename contains $query
     */
    public function search(string $pattern): FileList
    {

        if ( ! $this->exists()) {
            return FileList::create();
        }

        try {
            Tools::errors_as_exceptions();

            if (preg_valid($pattern)) {
                $result = static::scanFiles($this->path, true)->filter(fn(Filesystem $path) => $path->matches($pattern));
            } elseif (false !== strpbrk($pattern, static::GLOB_SEARCH)) {
                $result = $this->glob($pattern);
            } else {
                $result = static::scanFiles($this->path, true)->filter(fn(Filesystem $path) => $path->contains($pattern));
            }

            return $result;
        } catch (\Throwable) {
            return FileList::create();
        } finally {
            restore_error_handler();
        }
    }

    /**
     * Executes a glob search inside the directory
     */
    public function glob(string $pattern, int $flags = 0): FileList
    {

        $current = getcwd();
        try {
            Tools::errors_as_exceptions();
            $list = new FileList();

            if ($this->chdir()) {
                $result = glob($pattern, $flags);
                if ($result === false) {
                    return $list;
                }
                $list->append($result);
            }

            return $list;
        } finally {
            restore_error_handler();
            chdir($current);
        }
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
        return $this->filesIterator(static::scanFiles($this->path), $extensions, $hidden);
    }

    /**
     * List files recursively
     * @param string|array $extensions
     * @param bool $hidden
     * @return FileList
     */
    public function allFiles(string|array $extensions = [], bool $hidden = false): FileList
    {
        return $this->filesIterator(static::scanFiles($this->path, true), $extensions, $hidden);
    }

    /**
     * List directories
     *
     * @return FileList
     */
    public function directories(bool $recursive = false): FileList
    {

        $list = static::scanFiles($this->path);

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
        return $list->directories();
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
            return static::create($path);
        }
        return File::create($path);
    }

    public function getIterator(): Traversable
    {
        yield from static::scanFiles($this->path);
    }

}
