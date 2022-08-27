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
use function preg_exec,
             preg_test;

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

    /**
     * Create a range from python like slice
     * @link https://www.bestprog.net/en/2019/12/07/python-strings-access-by-indexes-slices-get-a-fragment-of-a-string-examples/
     */
    public static function fromSlice(string $slice, string|\Stringable $input): static
    {


        $length = mb_strlen((string) $input);

        if (preg_test('#^-?\d+$#', $slice)) {
            $start = intval($slice);
            $step = intval($start / abs($start));
            $stop = (abs($start) + 1) * $step;
            $output = static::create($start, $stop, $step);
        } elseif ($result = preg_exec('#^(-?\d+)?(?:\:(-?\d+)?)?(?:\:(-?\d+)?)?$#', $slice)) {

            @list(, $start, $stop, $step) = $result;

            if (is_null($step) || $step === '') {
                $step = 1;
            }

            $step = intval($step);

            if ((string) $start === '') {
                $start = $step > 0 ? 0 : -1;
            }

            if ((string) $stop === '') {
                $stop = $step > 0 ? $length : -$length - 1;
            }


            $output = static::create((int) $start, (int) $stop, $step);
        } else { throw new ValueError(sprintf('Invalid slice "%s"', $slice)); }




        if (isset($output)) {
            [$start, $stop, $step] = [$output->start, $output->stop, $output->step];
            if ($start < 0) {
                $start += $length;
            }
            if ($stop < 0) {
                $stop += $length;
            }

            if ($step > 0 ? $stop > $start : $stop < $start) {
                return $output;
            }
        }




        return $output;
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
