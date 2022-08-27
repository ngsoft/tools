<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use ArrayAccess,
    Countable,
    IteratorAggregate,
    JsonSerializable,
    Stringable,
    Traversable,
    ValueError;

/**
 * A Python like Range
 * @link https://docs.python.org/3/library/stdtypes.html#range
 * @phan-file-suppress PhanUnusedPublicMethodParameter
 */
class Range implements IteratorAggregate, ArrayAccess, Countable, JsonSerializable, Stringable
{

    public readonly int $start;
    public readonly int $stop;
    public readonly int $step;
    protected ?int $count = null;

    public static function create(int $start, ?int $stop = null, int $step = 1): static
    {
        return new static($start, $stop, $step);
    }

    public function __construct(
            int $start,
            ?int $stop = null,
            int $step = 1
    )
    {

        if ($step === 0) {
            throw new ValueError('Step cannot be 0');
        }

        if (is_null($stop)) {
            $stop = $start;
            $start = 0;
        }

        $this->start = $start;
        $this->stop = $stop;
        $this->step = $step;

        if ($step > 0 ? $stop <= $start : $stop >= $start) {
            $this->count = 0;
        }
    }

    public function getIterator(): Traversable
    {
        if ($this->isEmpty()) {
            return;
        }

        $offset = 0;

        while ( ! is_null($value = $this->offsetGet($offset))) {
            yield $value;
            $offset ++;
        }
    }

    protected function getValue(int $offset): int
    {
        return $this->start + ($offset * $this->step);
    }

    public function offsetExists(mixed $offset): bool
    {
        if ( ! is_int($offset) || $i < 0) {
            return false;
        }
        $value = $this->getValue($offset);

        return $this->step > 0 ? $value < $this->stop : $value > $this->stop;
    }

    public function offsetGet(mixed $offset): mixed
    {

        if ( ! $this->offsetExists($offset)) {
            return null;
        }

        return $this->getValue($offset);
    }

    public function toArray(): array
    {
        return iterator_to_array($this);
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    public function isEmpty(): bool
    {
        return $this->count === 0;
    }

    public function count(): int
    {

        return $this->count ??= iterator_count($this);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        // nothing to do
    }

    public function offsetUnset(mixed $offset): void
    {
        // nothing to do
    }

    public function __serialize(): array
    {
        return[$this->start, $this->stop, $this->step, $this->count];
    }

    public function __unserialize(array $data): void
    {
        [$this->start, $this->stop, $this->step, $this->count] = $data;
    }

    public function __toString(): string
    {
        return sprintf('%d:%d:%d', $this->start, $this->stop, $this->step);
    }

}
