<?php

declare(strict_types=1);

namespace NGSOFT\Types;

use ArrayAccess,
    Countable,
    JsonSerializable,
    Stringable,
    Throwable;

/**
 * Python like Collection
 */
abstract class pCollection extends pReversible implements Countable, ArrayAccess, JsonSerializable, Stringable
{

    use Sized,
        Container;

    ////////////////////////////   Not in Python (we are using PHP)   ////////////////////////////

    /**
     * Exports pCollection to array
     */
    public function toArray(): array
    {

        $array = [];

        foreach ($this as $offset => $value) {
            if ($value instanceof self) {
                $value = $value->toArray();
            }
            $array [$offset] = $value;
        }

        return $array;
    }

    /**
     * Exports pCollection to json
     */
    public function toJson(int $flags = JSON_UNESCAPED_LINE_TERMINATORS | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR): string
    {
        return json_encode($this, $flags);
    }

    ////////////////////////////   Python Methods   ////////////////////////////




    protected function __repr__(): string
    {
        return $this->toJson();
    }

    /**
     * Return a shallow copy of the list
     */
    public function copy(): static
    {
        return clone $this;
    }

    /**
     * Checks if a value is a collection with the same items as current
     */
    public function equals(mixed $value): bool
    {

        if ($value instanceof self) {
            $value = $value->toArray();
        }

        if (is_array($value)) {
            return $value === $this->toArray();
        }

        return false;
    }

    ////////////////////////////   Interfaces   ////////////////////////////


    public function offsetExists(mixed $offset): bool
    {

        try {
            return $this[$offset] !== null;
        } catch (Throwable) {
            return false;
        }
    }

    public function __debugInfo(): array
    {
        return $this->toArray();
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    public function __toString(): string
    {
        return $this->__repr__();
    }

}
