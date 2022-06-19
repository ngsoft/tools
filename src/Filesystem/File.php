<?php

declare(strict_types=1);

namespace NGSOFT\Filesystem;

use ErrorException,
    InvalidArgumentException,
    NGSOFT\Tools,
    RuntimeException,
    SplFileInfo,
    SplFileObject,
    Stringable,
    ValueError;
use function blank,
             get_debug_type;

/**
 * File
 */
class File extends Filesystem implements \IteratorAggregate
{

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
     * Checks if file begins with '.'
     * 
     * @return bool
     */
    public function hidden(): bool
    {
        return str_starts_with($this->basename(), '.');
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

        if ( ! $this->exists()) {
            return null;
        }

        // isolation from the current context
        $closure = static function (array $data) {
            extract($data);
            unset($data);
            return func_get_arg(2) ? require_once func_get_arg(1) : require func_get_arg(1);
        };

        try {
            Tools::errors_as_exceptions();
            return $closure($data, $this->path, $once);
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

        if (count($split) > 0) {

            if ( ! blank($value = array_pop($split))) {
                return $value;
            }
        }

        return '';
    }

    /**
     * Get CRC32 Checksum
     *
     * @return string|false
     */
    public function hash(): string|false
    {

        if ( ! $this->exists()) {
            return false;
        }

        return hash_file('crc32', $this->path);
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
        if ( ! $this->exists()) {
            return [];
        }
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
    public function readAsJson(): mixed
    {

        if (blank($contents = $this->read())) {
            return null;
        }
        return json_decode($contents, true, flags: JSON_THROW_ON_ERROR);
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
            $contents = [];
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
     *
     * @param mixed $data
     * @return bool
     */
    public function writeJson(mixed $data): bool
    {
        if ($contents = json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) {
            return $this->write($contents);
        }
        return false;
    }

    public function getIterator(): \Traversable
    {
        yield from $this->getContents();
    }

}
