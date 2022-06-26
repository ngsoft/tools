<?php

declare(strict_types=1);

namespace NGSOFT\Filesystem;

use ErrorException,
    InvalidArgumentException,
    IteratorAggregate,
    JsonException;
use NGSOFT\{
    Lock\FileSystemLock, Tools
};
use Stringable,
    Throwable,
    Traversable,
    ValueError;
use const DATE_DB,
          SCRIPT_START;
use function blank,
             get_debug_type;

/**
 * Manages a File
 */
class File extends Filesystem implements IteratorAggregate
{

    protected ?string $hash = null;
    protected ?FileSystemLock $lock = null;
    protected array $tmpFiles = [];
    protected ?string $tmpFile = null;

    public function __construct(
            string $path,
    )
    {
        parent::__construct($path);
        if (is_dir($this->path)) {
            throw new InvalidArgumentException(sprintf('%s is a directory.', $path));
        }
    }

    public function __destruct()
    {
        while ($file = array_pop($this->tmpFiles)) {
            ! is_file($file) || unlink($file);
        }
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
        $real = $this->realpath();
        return $real && is_file($real);
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
            // initial value
            if ($hash && ! $this->hash && $this->exists()) {
                // check if file modified before script started
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
        try {
            Tools::errors_as_exceptions();
            return ! $this->exists() || unlink($this->path);
        } catch (Throwable) {
            return false;
        } finally {
            clearstatcache(false, $this->path);
        }
    }

    /** {@inheritdoc} */
    protected function doCopy(string|Filesystem $target, bool &$success = null): static
    {
        return $this->copy($target, $success);
    }

    /**
     * Copy File
     *
     * @param string|self $target new file
     * @param ?bool $success True if the operation succeeded
     * @return static a File instance for the target
     */
    public function copy(string|self $target, bool &$success = null): static
    {

        $dest = (string) $target;
        $target = $target instanceof self ? $target : new static($dest);

        $success = false;

        try {
            Tools::errors_as_exceptions();

            if ($this->exists()) {
                static::createDir(dirname($dest));
                // no need to copy if files are the same
                if ($target->hash() !== $this->hash()) {
                    $success = copy($this->path, $dest);
                } else { $success = true; }
            }
        } catch (\Throwable) {
            $success = false;
        } finally {
            restore_error_handler();
        }


        return $target;
    }

    public function delete(): bool
    {
        return $this->unlink();
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
     * Sets access and modification time of file
     *
     * @param int|null $mtime
     * @param int|null $atime
     * @return bool
     */
    public function touch(?int $mtime = null, ?int $atime = null): bool
    {
        static::createDir($this->dirname());
        return touch($this->path, $mtime, $atime);
    }

    /**
     * Loads file as an Iterator
     */
    public function getContents(): FileContents
    {
        return new FileContents($this);
    }

    /**
     * Creates file contents
     */
    public function createContents(): FileContents
    {
        return new FileContents($this, loaded: true);
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

        $path = $this->realpath() ?: $this->path;
        static::createDir(dirname($path));

        $retry = 0;

        while ($retry < 3) {

            try {
                Tools::errors_as_exceptions();

                $tmpfile = $this->tmpFile ??= $this->tmpFiles[] = $this->dirname() . DIRECTORY_SEPARATOR . uniqid('', true);

                if (file_put_contents($tmpfile, $filecontents) !== false) {
                    return rename($tmpfile, $path) && $this->chmod(0777);
                }
            } catch (Throwable) {
                $this->tmpFile = null;
            } finally {
                restore_error_handler();
            }
            $retry ++;
            wait();
        }


        return false;
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
     * Locks file access on concurrent requests
     */
    public function lock(int|float $seconds = 0, string $owner = ''): FileSystemLock
    {
        return $this->lock ??= new FileSystemLock($this, $seconds, $owner);
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
                'crc32' => $this->hash(),
                    //     'locked' => $this->lock()->isAcquired(),
                    //   'lock' => $this->lock(),
            ];
        }
        return $result;
    }

}
