<?php

declare(strict_types=1);

namespace NGSOFT\Filesystem;

use Countable,
    InvalidArgumentException,
    NGSOFT\Tools,
    RuntimeException,
    SplFileInfo,
    Stringable;
use function blank;

abstract class Filesystem implements Countable, Stringable
{

    protected ?SplFileInfo $info;
    protected string $path;

    public static function create(string $path): static
    {
        return new static($path);
    }

    public function __construct(
            string $path,
    )
    {
        if (blank($path)) {
            throw new InvalidArgumentException('Filename is empty.');
        }

        $this->path = Tools::normalize_path($path);
    }

    abstract public function exists(): bool;

    /**
     * File Path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    protected function createDir(string $dirname): void
    {
        if ( ! is_dir($dirname) || ! mkdir($dirname, 0777, true)) {
            throw new RuntimeException(sprintf('Cannot create directory %s', $dirname));
        }
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
            $success = rename($this->path, $target);
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
        return $this->exists() && chmod($filename, $permissions);
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

}
