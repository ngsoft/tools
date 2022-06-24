<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use NGSOFT\{
    Filesystem\File, Lock\FileLock, Lock\LockStore
};
use OutOfBoundsException,
    ValueError;
use function get_debug_type;

class JsonObject extends SimpleObject
{

    protected ?File $file = null;
    protected ?LockStore $lockStore = null;

    public static function fromJsonFile(string $filename, bool $recursive = true): static
    {

        $file = new File($filename);
        $lock = new FileLock($filename);
        if ($file->exists()) {
            try {
                if ($lock->block(20)) {
                    $data = $file->read();
                    $instance = static::fromJson($data);
                }
            } finally {
                $lock->release();
            }
        } else { $instance = new static(); }

        $instance->file = $file;
        $instance->lockStore = $lock;

        return $instance;
    }

    protected function load(): void
    {
        if ($this->file?->exists()) {
            try {

            } finally {
                $this->lockStore->release();
            }
        }
    }

    protected function update(?Collection $child = null): void
    {
        parent::update($child);
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

}
