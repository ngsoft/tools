<?php

declare(strict_types=1);

namespace NGSOFT\Filesystem;

use Countable,
    IteratorAggregate,
    Traversable;
use function is_stringable;

class FileList implements IteratorAggregate, Countable
{

    protected array $files = [];

    public static function create(array $files = [])
    {
        $instance = new static();
        $instance->import($files);
        return $instance;
    }

    protected function import(iterable $files)
    {

        $this->files = [];
        foreach ($files as $key => $value) {
            if (is_string($key) && file_exists($key)) {
                $this->append($key);
                continue;
            }
            if (is_string($value) && file_exists($value)) {
                $this->append($value);
                continue;
            }

            if ($value instanceof Filesystem) {
                $this->files[$value->getPath()] = $value;
            }
        }
    }

    public function append(string|iterable $files): void
    {

        if ( ! is_iterable($files)) {
            $files = [$files];
        }
        if ($files instanceof Filesystem) {
            $this->files[$files->getPath()] = $files;
            return;
        }
        foreach ($files as $file) {
            if ( ! is_stringable($file)) {
                continue;
            }
            $file = (string) $file;

            if ( ! file_exists($file)) {
                continue;
            }
            $this->files[$file] = is_dir($file) ? Directory::create($file) : File::create($file);
        }
    }

    /**
     * Filter results using callable
     *
     * @param callable $callable
     * @return iterable
     */
    public function filter(callable $callable): iterable
    {

        foreach ($this as $key => $value) {

            if ($callable($value, $key, $this)) {
                yield $key => $value;
            }
        }
    }

    public function toArray(): array
    {
        return array_keys($this->files);
    }

    public function count(): int
    {
        return count($this->files);
    }

    public function getIterator(): Traversable
    {
        foreach ($this->files as $file => $obj) {
            yield $file => $obj;
        }
    }

    public function keys(): iterable
    {
        return array_keys($this->files);
    }

    public function values(): iterable
    {
        return array_values($this->files);
    }

    public function __debugInfo(): array
    {
        return $this->files;
    }

}
