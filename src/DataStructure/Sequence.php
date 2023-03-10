<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

class Sequence implements \ArrayAccess, \Countable, \JsonSerializable, \NGSOFT\Type\ReversibleIterator
{

    protected array $values = [];

    public function offsetSet(mixed $offset, mixed $value): void
    {
        // not implemented
    }

    public function offsetUnset(mixed $offset): void
    {
        // not implemented
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
        return $this->toJson();
    }

    /**
     * Exports to array
     */
    public function toArray(): array
    {
        return $this->values;
    }

    /**
     * Exports pCollection to json
     */
    public function toJson(int $flags = JSON_UNESCAPED_LINE_TERMINATORS | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR): string
    {
        return json_encode($this, $flags);
    }

    /**
     * Checks if a value is a sequence with the same items as current
     */
    public function equals(mixed $value): bool
    {

        if ($value instanceof self)
        {
            $value = $value->toArray();
        }

        if (is_array($value))
        {
            return $value === $this->toArray();
        }

        return false;
    }

    /**
     * Checks if value is in sequence
     */
    public function contains(mixed $value): bool
    {
        return in_array($value, $this->values, true);
    }

}
