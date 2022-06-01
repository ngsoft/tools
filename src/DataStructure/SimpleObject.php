<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use OutOfBoundsException;
use function get_debug_type;

class SimpleObject extends ArrayAccessCommon
{

    public static function create(array $array = [], bool $recursive = false): static
    {
        return new static($array, $recursive);
    }

    protected function assertValidImport(array $import): void
    {

        foreach (array_keys($import) as $offset) {
            if (!is_int($offset) && !is_string($offset)) {
                throw new OutOfBoundsException(sprintf('%s only accepts offsets of type string|int, %s given.', static::class, get_debug_type($offset)));
            }
        }
    }

    protected function append(mixed $offset, mixed $value): void
    {

        if (null === $offset) {
            $this->storage[] = $value;
            return;
        }

        if (!is_int($offset) && !is_int($offset)) {
            throw new OutOfBoundsException(sprintf('%s only accepts offsets of type string|int, %s given.', static::class, get_debug_type($offset)));
        }

        $this->offsetUnset($offset);
        if ($value instanceof self) $value = $value->storage;
        $this->storage[$offset] = $value;
    }

    /**
     * Searches the array for a given value and returns the first corresponding key if successful
     *
     * @param mixed $value
     * @return int|string|null
     */
    public function search(mixed $value): int|string|null
    {
        if ($value instanceof self) $value = $value->storage;
        $offset = array_search($value, $this->storage, true);
        return $offset === false ? null : $offset;
    }

}
