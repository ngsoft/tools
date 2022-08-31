<?php

declare(strict_types=1);

namespace NGSOFT\Types;

use NGSOFT\Types\Traits\{
    IsReversible, IsSliceable
};
use Throwable,
    Traversable;
use function in_range;

/**
 * Python like sequence
 *
 * @phan-file-suppress PhanUnusedPublicMethodParameter
 */
abstract class pSequence extends pCollection implements pReversible
{

    use IsReversible,
        IsSliceable;

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

        $value = $this->getValue($value);

        while (is_null($stop) || $offset < $stop) {

            try {

                $_value = $this->getValue($this[$offset]);

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

    public function offsetGet(mixed $offset): mixed
    {
        if ( ! $this->count()) {
            throw IndexError::for($offset, $this);
        }


        $offset = $this->getOffset($offset);

        if (is_int($offset)) {
            if ( ! in_range($offset, 0, $this->count() - 1)) {
                throw IndexError::for($offset, $this);
            }

            return $this->data[$offset];
        }

        return $this->withData($offset->slice($this));
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->data);
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


        try {

            foreach (Range::of($this) as $offset) {
                yield $this[$offset];
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
