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
 * Python like read only sequence
 *
 * @phan-file-suppress PhanUnusedPublicMethodParameter
 */
abstract class pSequence extends pCollection implements pReversible
{

    use IsReversible,
        IsSliceable;

    abstract protected function __getitem__(int $offset): mixed;

    /**
     * Return first offset of value.
     * Raises ValueError if the value is not present.
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
                if ($this->getValue($this[$offset]) === $value) {
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

    public function offsetSet(mixed $offset, mixed $value): void
    {
        // not implemented
    }

    public function offsetUnset(mixed $offset): void
    {
        // not implemented
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
