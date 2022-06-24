<?php

declare(strict_types=1);

namespace NGSOFT\Filesystem;

use ErrorException,
    InvalidArgumentException,
    IteratorAggregate,
    JsonException,
    NGSOFT\Lock\FileSystemLock,
    RuntimeException,
    SplFileInfo,
    SplFileObject,
    Stringable,
    Traversable,
    ValueError;
use const SCRIPT_START;
use function blank,
             get_debug_type;

/**
 * Manages a File
 */
class File extends Filesystem implements IteratorAggregate
{

    protected ?string $hash = null;

    public function __construct(
            string $path,
    )
    {
        if (is_dir($path)) {
            throw new InvalidArgumentException(sprintf('%s is a directory.', $path));
        }


        parent::__construct($path);
    }

    /**
     * Get file directory
     *
     * @return Directory
     */
    public function getDirectory(): Directory
    {
        return Directory::create($this->dirname());
    }

    /**
     * Checks if file exists and is regular file
     *
     * @return bool
     */
    public function exists(): bool
    {
        return is_file($this->path);
    }

    /**
     * Check if crc checksum has changed
     *
     * @return bool
     */
    public function isModified(): bool
    {

        $hash = $this->hash();
        $changed = $this->hash !== $hash;

        try {

            if (
                    $changed &&
                    $this->exists() &&
                    ! $this->hash
            ) {
                return $this->mtime() < SCRIPT_START;
            }

            return $changed;
        } finally {
            $this->hash = $hash;
        }
    }

    /**
     * Deletes the file
     *
     * @return bool
     */
    public function unlink(): bool
    {
        return ! $this->exists() || unlink($this->path);
    }

    /**
     * Copy File
     *
     * @param string $target new file
     * @param ?bool $success True if the operation succeeded
     * @return static a File instance for the target
     */
    public function copy(string $target, bool &$success = null): static
    {

        $success = false;
        if ($this->exists()) {

            $this->createDir(dirname($target));

            $success = copy($this->path, $target);
        }

        return new static($target);
    }

    /**
     * Includes file as php file
     *
     * @param array $data data to extract
     * @param bool $once require_once
     * @return mixed
     */
    public function require(array $data = [], bool $once = false): mixed
    {

        try {
            return require_file($this->path, $data, $once);
        } catch (ErrorException) {
            return null;
        } finally { restore_error_handler(); }
    }

    /**
     * Get file name without extension
     *
     * @return string
     */
    public function name(): string
    {
        return pathinfo($this->path, PATHINFO_FILENAME);
    }

    /**
     * Get the last file extension
     *
     * @return string
     */
    public function extension(): string
    {

        $split = explode('.', $this->path);

        if (count($split) > 1) {

            if ( ! blank($value = array_pop($split))) {
                return $value;
            }
        }

        return '';
    }

    /**
     * Get CRC32 Checksum
     */
    public function hash(): string|null
    {

        if ( ! $this->exists()) {
            return null;
        }

        return hash_file('crc32', $this->path) ?: null;
    }

    /**
     * Gets an SplFileInfo object for the file
     *
     * @return SplFileInfo
     */
    public function getFileInfo(): SplFileInfo
    {
        $this->info = $this->info ?? new SplFileInfo($this->path);
        return $this->info;
    }

    /**
     * Gets an SplFileObject object for the file
     *
     * @param string $mode
     * @return SplFileObject
     */
    public function openFile(string $mode = 'r'): SplFileObject
    {
        return $this->getFileInfo()->openFile($mode);
    }

    /**
     * Sets access and modification time of file
     *
     * @param int|null $mtime
     * @param int|null $atime
     * @return bool
     */
    public function touch(?int $mtime = null, ?int $atime = null): bool
    {
        return touch($this->path, $mtime, $atime);
    }

    /**
     * Loads file as an Iterator
     *
     * @return FileContents
     */
    public function getContents(): FileContents
    {
        return new FileContents($this, $this->readAsArray());
    }

    /**
     * Loads file
     *
     * @return string
     */
    public function read(): string
    {
        if ( ! $this->exists()) {
            return '';
        }

        return file_get_contents($this->path) ?: '';
    }

    /**
     * Read file as array of lines
     *
     * @return string[]
     */
    public function readAsArray(): array
    {
        $contents = $this->read();
        if (empty($contents)) {
            return [];
        }
        return explode("\n", $contents);
    }

    /**
     * Decodes json file
     *
     * @return mixed
     */
    public function readJson(): mixed
    {

        if (blank($contents = $this->read())) {
            return null;
        }
        $result = json_decode($contents, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonException(json_last_error_msg());
        }
        return $result;
    }

    /**
     * Save File
     *
     * @param string|string[]|Stringable|Stringable[] $contents
     * @return bool
     */
    public function write(string|array|Stringable $contents): bool
    {

        $dirname = $this->dirname();
        if (( ! file_exists($dirname)) && ! mkdir($dirname, 0777, true)) {
            throw new RuntimeException(sprintf('Cannot create directory %s', $dirname));
        }

        if ( ! is_array($contents)) {
            $contents = [$contents];
        }

        $filecontents = '';

        foreach ($contents as $line) {
            if ( ! blank($filecontents)) { $filecontents .= "\n"; }

            if ( ! is_string($line) && $line instanceof \Stringable === false) {
                throw new ValueError(sprintf('Cannot save %s, invalid type %s', $this->path, get_debug_type($line)));
            }

            $filecontents .= (string) $line;
        }

        return file_put_contents($this->path, $filecontents) !== false;
    }

    /**
     * Dumps data to json
     */
    public function writeJson(mixed $data, int $flags = JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES): bool
    {
        $contents = json_encode($data, $flags);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonException(json_last_error_msg());
        }

        return $contents !== false && $this->write($contents);
    }

    public function getIterator(): Traversable
    {
        yield from $this->getContents();
    }

    /**
     * Locks file access on concurrent access
     */
    public function lock(int|float $seconds = 0, string $owner = ''): FileSystemLock
    {
        return new FileSystemLock($this->path, $seconds, $owner);
    }

}
