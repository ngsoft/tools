<?php

declare(strict_types=1);

namespace NGSOFT\Types;

use ArrayAccess,
    Countable,
    JsonSerializable,
    Stringable;

/**
 * Python like Collection
 */
abstract class pCollection implements Countable, pIterable, ArrayAccess, JsonSerializable, Stringable
{

    protected array $data = [];

    /**
     * Exports pCollection to array
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Checks if collection has value
     */
    public function contains(mixed $value): bool
    {

        if (is_null($value)) {
            return false;
        }


        return $this->count($value) > 0;
    }

    /**
     * Count number of occurences of value
     * if value is null returns the collections size
     */
    public function count(mixed $value = null): int
    {

        if (is_null($value)) {
            return count($this->data);
        }

        $value = $this->getValue($value);
        $cnt = 0;
        foreach ($this as $_value) {
            if ($value === $this->getValue($_value)) {
                $cnt ++;
            }
        }
        return $cnt;
    }

    ////////////////////////////   Helpers   ////////////////////////////

    /**
     * Translate pCollection value
     */
    protected function getValue(mixed $value): mixed
    {

        if ($value instanceof self) {
            return $value->data;
        }

        return $value;
    }

    ////////////////////////////   Interfaces   ////////////////////////////



    public function __serialize(): array
    {
        return [$this->data];
    }

    public function __unserialize(array $data): void
    {
        [$this->data] = $data;
    }

    public function __debugInfo(): array
    {
        return $this->data;
    }

    public function jsonSerialize(): mixed
    {
        return $this->data;
    }

    public function __toString(): string
    {
        return json_encode($this, JSON_UNESCAPED_LINE_TERMINATORS | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

}
