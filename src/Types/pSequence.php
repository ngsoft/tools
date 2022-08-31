<?php

declare(strict_types=1);

namespace NGSOFT\Types;

use Throwable,
    Traversable;
use function in_range;

/**
 * Python like read only sequence
 *
 * @phan-file-suppress PhanUnusedPublicMethodParameter
 */
abstract class pSequence extends pCollection
{

    abstract protected function __getitem__(int $offset): mixed;

    protected function __contains__(mixed $value): bool
    {
        if (is_null($value)) {
            return false;
        }

        return $this->count($value) > 0;
    }

    /** {@inheritdoc} */
    protected function __iter__(): iterable
    {

        try {

            for ($offset = 0; $offset < $this->__len__(); $offset ++ ) {
                yield $this[$offset];
            }
        } catch (Throwable) {
            return;
        }
    }

    /** {@inheritdoc} */
    protected function __reversed__(): Traversable
    {

        try {

            for ($offset = -1; $offset >= -$this->__len__(); $offset --) {
                yield $this[$offset];
            }
        } catch (Throwable) {
            return;
        }

        foreach (Range::of($this)->reverse() as $offset) {
            yield $this[$offset];
        }
    }

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

    /**
     * Count number of occurences of value
     * if value is null returns the collections size
     */
    public function count(mixed $value = null): int
    {

        // Countable __len__()
        if (is_null($value)) {
            return $this->__len__();
        }

        $value = $this->getValue($value);
        $cnt = 0;
        foreach ($this as $_value) {
            if ($value === $this->getValue($_value)) {
                $cnt ++;
            }
        }
        return $cnt;
    }

    /**
     * Translate negative offset as real offset,
     * Slice offset as list of offsets
     */
    protected function getOffset(Slice|int|string|null $offset): Slice|int
    {

        if (is_null($offset)) {
            return $this->count();
        }
        if (is_string($offset) && ! Slice::isValid($offset)) {
            throw IndexError::for($offset, $this);
        }


        if (is_int($offset) && $offset < 0) {
            $offset += $this->count();

            if ($offset === -1 && ! $this->count()) {
                $offset = 0;
            }
        } elseif (is_string($offset)) {
            $offset = Slice::of($offset);
        }

        return $offset;
    }

    public function offsetGet(mixed $offset): mixed
    {
        if ( ! $this->__len__()) {
            throw IndexError::for($offset, $this);
        }


        $offset = $this->getOffset($offset);

        if (is_int($offset)) {
            if ( ! in_range($offset, 0, $this->count() - 1)) {
                throw IndexError::for($offset, $this);
            }

            return $this->__getitem__($offset);
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

}
