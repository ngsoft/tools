<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use OutOfBoundsException;
use function get_debug_type;

class SimpleObject extends Collection
{

    protected function assertValidImport(array $import): void
    {

        foreach (array_keys($import) as $offset) {
            if ( ! is_int($offset) && ! is_string($offset)) {
                throw new OutOfBoundsException(sprintf('%s only accepts offsets of type string|int, %s given.', static::class, get_debug_type($offset)));
            }


            if ($this->recursive && is_array($import[$offset])) {
                $this->assertValidImport($import[$offset]);
            }
        }
    }

    protected function append(mixed $offset, mixed $value): int|string
    {

        if ($value instanceof self) {
            $value = $value->storage;
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

    /**
     * Searches the array for a given value and returns the first corresponding key if successful
     *
     * @param mixed $value
     * @return int|string|null
     */
    public function search(mixed $value): int|string|null
    {
        if ($value instanceof self) { $value = $value->storage; }
        $offset = array_search($value, $this->storage, true);
        return $offset === false ? null : $offset;
    }

    /** {@inheritdoc} */
    public function &__get(string $name): mixed
    {
        $value = $this->offsetGet($name);
        return $value;
    }

    /** {@inheritdoc} */
    public function __set(string $name, mixed $value): void
    {
        $this->offsetSet($name, $value);
    }

    /** {@inheritdoc} */
    public function __unset(string $name): void
    {
        $this->offsetUnset($name);
    }

    /** {@inheritdoc} */
    public function __isset(string $name): bool
    {
        return $this->offsetExists($name);
    }

}
