<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use NGSOFT\{
    Filesystem\File, Lock\FileLock, Lock\LockStore
};
use OutOfBoundsException,
    RuntimeException,
    ValueError;
use function get_debug_type;

class JsonObject extends SimpleObject
{

    protected ?File $file = null;
    protected ?LockStore $lockStore = null;
    protected ?string $hash = null;

    public static function fromJsonFile(string $filename, bool $recursive = true): static
    {

        $file = new File($filename);
        $lock = new FileLock($filename, 60);
        $data = [];
        if ($file->exists()) {
            $lock->block(20);
            $data = $file->readJson();
        }
        $instance = static::create($data, $recursive);

        $instance->file = $file;
        $instance->lockStore = $lock;
        $instance->hash = $file->hash();

        return $instance;
    }

    protected function block(): bool
    {
        return $this->lockStore?->block(20) ?? true;
    }

    protected function load(): void
    {

        if ($this->file?->exists()) {

            if ($this->hash !== $this->file->hash()) {
                $this->lockStore->block(20);
                $this->storage = $this->file->readJson();
            }
        }
    }

    protected function update(?Collection $child = null): void
    {
        parent::update($child);
        if ($this->file) {

            $this->lockStore->block(20);
            if ($this->file->writeJson($this->storage)) {
                wait();
                $this->hash = $this->file->hash();
            } else {
                throw new RuntimeException(sprintf('Cannot update file %s', $this->file));
            }
        }
    }

    protected function append(mixed $offset, mixed $value): int|string
    {
        if ($value instanceof self) {
            $value = $value->storage;
        }

        if ( ! is_scalar($value) && ! is_array($value) && ! is_null($value)) {
            throw new ValueError(sprintf('%s can only use types string|int|float|bool|null|array, %s given.', $this, get_debug_type($value)));
        }

        if (null === $offset) {
            $this->storage[] = $value;
            return array_key_last($this->storage);
        }

        if ( ! is_int($offset) && ! is_string($offset)) {
            throw new OutOfBoundsException(sprintf('%s only accepts offsets of type string|int, %s given.', static::class, get_debug_type($offset)));
        }
        unset($this->storage[$offset]);
        $this->storage[$offset] = $value;

        return $offset;
    }

    public function offsetExists(mixed $offset): bool
    {

        $this->load();
        return parent::offsetExists($offset);
    }

    public function &offsetGet(mixed $offset): mixed
    {

        $this->load();
        $value = &parent::offsetGet($offset);
        return $value;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {

        $this->load();
        parent::offsetSet($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {

        $this->load();
        parent::offsetUnset($offset);
    }

    public function count(): int
    {
        $this->load();
        return parent::count();
    }

}
