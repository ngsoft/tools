<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

class SimpleObject extends Collection
{

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
        $value = &$this->offsetGet($name);
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
