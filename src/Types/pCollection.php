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

    protected array $data = [];

    ////////////////////////////   Not in Python (we are using PHP)   ////////////////////////////

    /**
     * Exports pCollection to array
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Exports pCollection to json
     */
    public function toJson(int $flags = JSON_UNESCAPED_LINE_TERMINATORS | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR): string
    {
        return json_encode($this, $flags);
    }

    ////////////////////////////   Python Methods   ////////////////////////////

    /**
     * Return a shallow copy of the list
     */
    public function copy(): static
    {
        return clone $this;
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

    protected function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    protected function withData(array $data): static
    {
        return $this->copy()->setData($data);
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

}
