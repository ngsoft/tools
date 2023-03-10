<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use ArrayAccess,
    Countable,
    Generator;
use NGSOFT\{
    Tools\TypeCheck, Traits\ObjectLock, Type\ReversibleIterator, Type\Sort
};
use Stringable,
    Traversable,
    ValueError;
use function get_debug_type,
             is_list,
             mb_str_split;

/**
 * The SimpleIterator can iterate everything in any order
 */
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

    ////////////////////////////   Static methods   ////////////////////////////

    /**
     * Create a new SimpleIterator
     */
    public static function of(iterable $iterable): static
    {
        return new static($iterable);
    }

    /**
     * Creates a new SimpleIterator that iterates each characters
     */
    public static function ofStringable(string|Stringable $value): static
    {
        $value = (string) $value;
        return new static($value === '' ? [] : mb_str_split($value));
    }

    /**
     * Creates an iterator from a list
     *
     * @param iterable|ArrayAccess&Countable $value
     */
    public static function ofList($value): static
    {


        if (is_iterable($value))
        {
            return static::of($value);
        }

        TypeCheck::assertTypeMethod(
                [static::class, __FUNCTION__, 0], $value,
                TypeCheck::TYPE_ARRAYACCESS
        );

        if (is_list($value))
        {
            $iterator = new static([]);
            foreach (Range::of($value) as $offset)
            {
                $iterator->append($offset, $value[$offset]);
            }
            $iterator->lock();

            return $iterator;
        }

        throw new ValueError(sprintf('%s cannot determine list of keys.', get_debug_type($value)));
    }

    ////////////////////////////   Implementation   ////////////////////////////

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
    protected function yieldOffsets(array $offsets): Generator
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
