<?php

declare(strict_types=1);

namespace NGSOFT\Filesystem;

use Countable,
    IteratorAggregate,
    Traversable;
use function is_stringable,
             NGSOFT\Tools\map;

/**
 * File list Iterator
 */
class FileList implements IteratorAggregate, Countable
{

    protected array $files = [];

    public static function create(array $files = [])
    {
        $instance = new static();
        $instance->append($files);
        return $instance;
    }

    /**
     * Adds a file to the list
     */
    public function append(string|iterable|Filesystem $files): void
    {

        if ( ! is_iterable($files)) {
            $files = [$files];
        }
        if ($files instanceof Filesystem) {
            $this->files[$files->realpath()] = $files;
            return;
        }
        foreach ($files as $relative => $file) {

            if ($file instanceof Filesystem && $file->exists()) {
                $this->files[is_string($relative) ? $relative : $file->realpath()] = $file;
                continue;
            }

            if ( ! is_stringable($file)) {
                continue;
            }

            $file = (string) $file;

            $relative = is_string($relative) ? $relative : $file;

            if ( ! file_exists($file)) {
                continue;
            }
            $this->files[$relative] = is_dir($file) ? Directory::create($file) : File::create($file);
        }
    }

    /**
     * Returns only files
     */
    public function files(): static
    {
        static $handler;
        $handler ??= fn($file) => $file instanceof File;
        return $this->filter($handler);
    }

    /**
     * Returns only directories
     */
    public function directories(): static
    {
        static $handler;
        $handler ??= fn($file) => $file instanceof Directory;
        return $this->filter($handler);
    }

    /**
     * Filter results using callable
     *
     * @param callable $callable
     * @return static
     */
    public function filter(callable $callable): static
    {

        $result = [];
        foreach ($this as $key => $value) {

            if ($callable($value, $key, $this)) {
                $result[$key] = $value;
            }
        }
        return static::create($result);
    }

    /**
     * Returns files realpaths
     */
    public function toArray(): array
    {
        return map(fn($file) => $file->getPath(), $this);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function count(): int
    {
        return count($this->files);
    }

    /**
     * @return Traversable<string, File|Directory>
     */
    public function getIterator(): Traversable
    {
        foreach ($this->files as $file => $obj) {
            yield $file => $obj;
        }
    }

    /**
     * @return string[]
     */
    public function keys(): iterable
    {
        return array_keys($this->files);
    }

    /**
     * @return File[]|Directory[]
     */
    public function values(): iterable
    {
        return array_values($this->files);
    }

    public function __serialize(): array
    {
        return [$this->files];
    }

    public function __unserialize(array $data): void
    {
        list($this->files) = $data;
    }

    public function __debugInfo(): array
    {
        return $this->files;
    }

}
