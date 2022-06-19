<?php

declare(strict_types=1);

namespace NGSOFT\Filesystem;

use Countable,
    IteratorAggregate,
    Traversable;

class FileList implements IteratorAggregate, Countable
{

    protected array $files = [];

    public static function create(array $files)
    {
        $instance = new static();
        $instance->import($files);
        return $instance;
    }

    protected function import(array $files)
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

    public function append(string $file): void
    {
        if ( ! file_exists($file)) {
            return;
        }
        $this->files[$file] = is_dir($file) ? Directory::create($file) : File::create($file);
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

}
