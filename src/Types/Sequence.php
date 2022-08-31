<?php

declare(strict_types=1);

namespace NGSOFT\Types;

use NGSOFT\{
    Tools, Types\Traits\IsReversible
};
use Throwable,
    Traversable;
use function NGSOFT\Tools\some;

/**
 * @phan-file-suppress PhanUnusedPublicMethodParameter
 */
abstract class Sequence implements pReversible, pCollection
{

    use IsReversible;

    /** {@inheritdoc} */
    public function contains(mixed $value): bool
    {
        return some(fn($_value) => $value === $_value, $this);
    }

    /**
     * Return first offset of value.
     * Raises ValueError if the value is not present.
     *
     */
    public function index(mixed $value, int $start = 0, ?int $stop = null): int
    {

        if ($start < 0) {
            $start = max($this->count() + $start, 0);
        }

        if ($stop < 0) {
            $stop += $this->count();
        }


        $offset = $start;

        while (is_null($stop) || $offset < $stop) {

            try {

                $_value = $this[$offset];
                if ($_value === $value) {
                    return $offset;
                }
            } catch (Throwable) {
                break;
            }
            $offset ++;
        }


        throw ValueError::for($value, $this);
    }

    public function count(): int
    {
        return 0;
    }

    /**
     * return number of occurrences of value
     */
    public function countValue(mixed $value): int
    {
        return Tools::countValue($value, $this);
    }

    public function offsetGet(mixed $offset): mixed
    {
        throw IndexError::for($offset, $this);
    }

    public function offsetExists(mixed $offset): bool
    {

        try {
            return $this[$offset] !== null;
        } catch (Throwable) {
            return false;
        }
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        // nothing to do
    }

    public function offsetUnset(mixed $offset): void
    {
        // nothing to do
    }

    /** {@inheritdoc} */
    public function getIterator(): Traversable
    {
        $offset = 0;

        try {
            while ($this->offsetExists($offset)) {
                $value = $this[$offset];
                yield $value;
                $offset ++;
            }
        } catch (Throwable) {
            return;
        }
    }

    /** {@inheritdoc} */
    public function getReverseIterator(): Traversable
    {

        foreach (Range::of($this)->getReverseIterator() as $offset) {
            yield $this[$offset];
        }
    }

}
