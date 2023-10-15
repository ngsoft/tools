<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

class SimpleObject extends Collection
{
    public function __get(string $name): mixed
    {
        return $this->offsetGet($name);
    }

    public function __set(string $name, mixed $value): void
    {
        $this->offsetSet($name, $value);
    }

    public function __unset(string $name): void
    {
        $this->offsetUnset($name);
    }

    public function __isset(string $name): bool
    {
        return $this->offsetExists($name);
    }

    /**
     * Searches the array for a given value and returns the first corresponding key if successful.
     */
    public function search(mixed $value): int|string|null
    {
        if ($value instanceof self)
        {
            $value = $value->storage;
        }
        $offset = array_search($value, $this->storage, true);
        return false === $offset ? null : $offset;
    }
}
