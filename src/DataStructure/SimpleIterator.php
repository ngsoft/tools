<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use NGSOFT\{
    Traits\ObjectLock, Type\ReversibleIterator, Type\Sort
};
use Traversable;

class SimpleIterator implements ReversibleIterator
{

    use ObjectLock;

    protected array $keys = [];
    protected array $values = [];

    public function __construct(
            protected iterable $iterator
    )
    {

    }

    /**
     * @internal Used for static method
     */
    protected function append(mixed $key, mixed $value): void
    {

        if ( ! $this->isLocked())
        {
            $this->keys[] = $key;
            $this->values[] = $value;
        }
    }

    /**
     * @internal Yield Offsets Value
     */
    protected function yieldOffsets(array $offsets): \Generator
    {

        foreach ($offsets as $offset)
        {
            yield $this->keys[$offset] => $this->values[$offset];
        }
    }

    /**
     * @internal Get iterator offsets
     */
    protected function getOffsets(): array
    {
        if ( ! $this->isLocked())
        {
            foreach ($this->iterator as $key => $value)
            {
                $this->append($key, $value);
            }
            $this->lock();
        }

        return array_keys($this->keys);
    }

    public function count(): int
    {
        return count($this->getOffsets());
    }

    public function entries(Sort $sort = Sort::ASC): iterable
    {
        $offsets = $this->getOffsets();

        if ($sort === Sort::DESC)
        {
            $offsets = array_reverse($sort);
        }

        yield from $this->yieldOffsets($offsets);
    }

    public function getIterator(): Traversable
    {
        yield from $this->entries();
    }

    public function getReverseIterator(): Traversable
    {
        yield from $this->entries(Sort::DESC);
    }

}
