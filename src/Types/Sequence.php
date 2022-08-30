<?php

declare(strict_types=1);

namespace NGSOFT\Types;

use NGSOFT\Types\Traits\IsReversible,
    Throwable,
    Traversable;
use function NGSOFT\Tools\some;

class Sequence implements Reversible, Collection
{

    use IsReversible;

    /** {@inheritdoc} */
    public function contains(mixed $value): bool
    {
        return some(fn($_value) => $value === $_value, $this);
    }

    public function index(mixed $value, int $start = 0, ?int $stop = null): int
    {

    }

    public function offsetExists(mixed $offset): bool
    {

    }

    public function offsetGet(mixed $offset): mixed
    {
        throw IndexError::for($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {

    }

    public function offsetUnset(mixed $offset): void
    {

    }

    public function count(): int
    {

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
