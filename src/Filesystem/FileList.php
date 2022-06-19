<?php

declare(strict_types=1);

namespace NGSOFT\Filesystem;

use ArrayIterator,
    Countable,
    IteratorAggregate,
    Traversable;

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

        foreach ($files as $file) {

            if ($file instanceof Filesystem) {
                $this->files[$file->getPath()] = $file;
                continue;
            }
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

        yield from new ArrayIterator(array_keys($this->files));
    }

    public function values(): iterable
    {
        yield from new ArrayIterator(array_values($this->files));
    }

}
