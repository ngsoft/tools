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
use function NGSOFT\Tools\map;

/**
 * A Python like Range
 * @link https://docs.python.org/3/library/stdtypes.html#range
 * @phan-file-suppress PhanUnusedPublicMethodParameter
 */
class Range implements IteratorAggregate, ArrayAccess, Countable, JsonSerializable, Stringable
{

    protected int $start;
    protected int $stop;
    protected int $step;
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

    protected function isValidSlice(Text $text): bool
    {


        [$start, $stop, $len] = [$this->start, $this->stop, count($text)];

        if ( ! in_range($start, -$len, $len - 1)) {
            return false;
        }

        if ( ! in_range($stop, -$len - 1, $len)) {
            return false;
        }


        return $this->step > 0 ? $stop > $start : $stop < $start;
    }

    /**
     * Get a slice from a stringable using current range
     */
    public function slice(mixed $text): string
    {


        $text = Text::of($text);
        $result = '';

        if ( ! $this->isValidSlice($text)) {
            return $result;
        }



        foreach ($this as $offset) {

            $sign = $offset > 0 ? 1 : -1;

            if (isset($prev) && $prev !== $sign) {
                break;
            }

            $result .= $string->at($offset);
            $prev = $sign;
        }


        return $result;
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
