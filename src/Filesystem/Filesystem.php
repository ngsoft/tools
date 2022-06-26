<?php

declare(strict_types=1);

namespace NGSOFT\Filesystem;

use Countable,
    InvalidArgumentException,
    RuntimeException,
    SplFileInfo,
    Stringable;
use const DATE_DB;
use function blank,
             preg_exec,
             str_starts_with;

abstract class Filesystem implements Countable, Stringable
{

    protected ?SplFileInfo $info;
    protected string $path;

    public static function create(string $path): static
    {
        return new static($path);
    }

    /**
     * Checks if path begins with (drive:)[/\]
     */
    protected static function isRelativePath(string $path): bool
    {
        return ! preg_match('#^(?:(?:\w+:)?[\/\\\]+)#', $path);
    }

    /**
     * Get absolute path
     */
    protected static function getAbsolute(string $path)
    {
        return normalize_path(static::isRelativePath($path) ? getcwd() . DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Crates a dir fi it does not exists
     */
    protected static function createDir(string $dirname): void
    {

        $dirname = static::getAbsolute($dirname);

        if ( ! is_dir($dirname) && ! mkdir($dirname, 0777, true)) {
            throw new RuntimeException(sprintf('Cannot create directory %s', $dirname));
        }
    }

    public function __construct(
            string $path,
    )
    {
        if (blank($path)) {
            throw new InvalidArgumentException('Filename is empty.');
        }


        $this->path = self::getAbsolute($path);
    }

    /**
     * Check if file exists
     */
    abstract public function exists(): bool;

    /**
     * Gets an SplFileInfo object for the file
     *
     * @return SplFileInfo
     */
    public function getFileInfo(): SplFileInfo
    {
        return $this->info ??= new SplFileInfo($this->path);
    }

    /**
     * Checks if file basename matches regular expression
     */
    public function matches(string $pattern, int $limit = 1): array|false
    {
        return preg_exec($pattern, $this->basename(), $limit);
    }

    /**
     * Get Realpath
     */
    public function realpath(): string|false
    {
        return realpath($this->path);
    }

    /**
     * File Path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Checks if file begins with '.'
     *
     * @return bool
     */
    public function hidden(): bool
    {
        return str_starts_with($this->basename(), '.');
    }

    /**
     * Move/Rename file
     *
     * @param string $target new file
     * @param ?bool $success True if the operation succeeded
     * @return static a File instance for the target
     */
    public function move(string $target, bool &$success = null): static
    {

        $success = false;

        if ($this->exists()) {
            $this->createDir(dirname($target));
            $success = rename($this->path, self::getAbsolute($target));
        }

        return new static($target);
    }

    /**
     * Can Read
     * @return bool
     */
    public function readable(): bool
    {
        return is_readable($this->path);
    }

    /**
     * Can write
     * @return bool
     */
    public function writable(): bool
    {
        return is_writable($this->path);
    }

    /**
     * Changes file mode
     *
     * @param int $permissions
     * @return bool
     */
    public function chmod(int $permissions): bool
    {
        return $this->exists() && chmod($this->path, $permissions);
    }

    /**
     * Gets last access time of the file
     * @return int
     */
    public function atime(): int
    {
        return fileatime($this->path) ?: 0;
    }

    /**
     * Gets the last modified time
     *
     * @return int
     */
    public function mtime(): int
    {
        return filemtime($this->path) ?: 0;
    }

    /**
     * Gets the inode change time
     *
     * @return int
     */
    public function ctime(): int
    {
        return filectime($this->path) ?: 0;
    }

    /**
     * File size
     *
     * @return int
     */
    public function size(): int
    {
        return filesize($this->path) ?: 0;
    }

    /**
     * File name without directory
     *
     * @return string
     */
    public function basename(): string
    {
        return basename($this->path);
    }

    /**
     * @return string
     */
    public function dirname(): string
    {
        return dirname($this->path);
    }

    public function count(): int
    {
        return $this->size();
    }

    public function __toString(): string
    {
        return $this->path;
    }

    public function __debugInfo(): array
    {
        $result = [
            'path' => $this->path,
        ];

        if ($this->exists()) {
            $result += [
                'ctime' => date(DATE_DB, $this->ctime()),
                'mtime' => date(DATE_DB, $this->mtime()),
            ];
        }
        return $result;
    }

}
