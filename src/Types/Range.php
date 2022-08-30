<?php

declare(strict_types=1);

namespace NGSOFT\Types;

use ArrayAccess,
    Countable,
    JsonSerializable,
    NGSOFT\Types\Traits\IsReversible,
    Stringable,
    Traversable,
    ValueError;

/**
 * A Python like Range Implementation
 * @link https://docs.python.org/3/library/stdtypes.html#range
 * @phan-file-suppress PhanUnusedPublicMethodParameter
 */
class Range implements Reversible, ArrayAccess, Countable, JsonSerializable, Stringable
{

    use IsReversible;

    protected int $start;
    protected int $stop;
    protected int $step;
    protected ?int $count = null;

    public static function create(int $start, ?int $stop = null, int $step = 1): static
    {
        return new static($start, $stop, $step);
    }

    /**
     * Get a range for a Countable
     */
    public static function of(Countable|array $countable): static
    {
        return new static(0, count($countable));
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
    }

    public function getStart(): int
    {
        return $this->start;
    }

    public function getStop(): int
    {
        return $this->stop;
    }

    public function getStep(): int
    {
        return $this->step;
    }

    public function getReverseIterator(): \Traversable
    {
        if ($this->isEmpty()) {
            return;
        }

        $offset = $this->count() - 1;

        while ( ! is_null($value = $this->offsetGet($offset))) {
            yield $value;
            $offset --;
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
        if ( ! is_int($offset) || $offset < 0) {
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
        return $this->step > 0 ? $this->stop <= $this->start : $this->stop >= $this->start;
    }

    public function count(): int
    {

        if (is_null($this->count)) {
            if ($this->isEmpty()) {
                return $this->count = 0;
            }

            [$min, $max, $step] = [$this->start, $this->stop, abs($this->step)];

            if ($min > $max) {
                [$min, $max] = [$max, $min];
            }

            return $this->count = intval(ceil(($max - $min) / $step));
        }
        return $this->count;
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

    public function __clone()
    {
        $this->count = null;
    }

    public function __unserialize(array $data): void
    {
        [$this->start, $this->stop, $this->step, $this->count] = $data;
    }

    public function __toString(): string
    {
        return sprintf('%s(%d, %d, %d)', static::class, $this->start, $this->stop, $this->step);
    }

}
