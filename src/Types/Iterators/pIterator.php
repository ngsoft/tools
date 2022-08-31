<?php

declare(strict_types=1);

namespace NGSOFT\Types\Iterators;

use ArrayAccess,
    Countable;
use NGSOFT\{
    Tools\TypeCheck, Traits\ObjectLock, Types\pReversible, Types\Range, Types\Traits\IsReversible, Types\ValueError
};
use Stringable,
    Traversable;
use function get_debug_type,
             mb_str_split,
             NGSOFT\Types\is_list;

/**
 * Basic (Reverse) Iterator Proxy
 */
class pIterator implements pReversible, Countable
{

    use IsReversible,
        ObjectLock;

    protected array $keys = [];
    protected array $values = [];

    ////////////////////////////   Static Methods   ////////////////////////////

    /**
     * Creates a new pIterator
     */
    public static function of(iterable $iterable): static
    {
        return new static($iterable);
    }

    /**
     * Creates a new pIterator that iterates each characters
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

        if (is_iterable($value)) {
            return static::of($value);
        }

        TypeCheck::assertTypeMethod(
                [static::class, __FUNCTION__, 0], $value,
                TypeCheck::TYPE_ARRAYACCESS
        );

        if (is_list($value)) {

            $iterator = new static([]);

            foreach (Range::of($value) as $offset) {

                $iterator->append($offset, $value[$offset]);
            }

            $iterator->lock();
        }


        throw new ValueError(sprintf('%s cannot determine list of keys.', get_debug_type($value)));
    }

    ////////////////////////////   Implementation   ////////////////////////////

    /**
     * Creates a new pIterator
     */
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

        if ( ! $this->isLocked()) {
            $this->keys[] = $key;
            $this->values[] = $value;
        }
    }

    /**
     * Yield Offset Value
     */
    protected function yieldOffset(int $offset): iterable
    {
        yield $this->keys[$offset] => $this->values[$offset];
    }

    protected function getOffsets(): array
    {
        if ( ! $this->isLocked()) {
            foreach ($this->iterator as $key => $value) {
                $this->append($key, $value);
            }
        }

        return Range::of($this->keys)->toArray();
    }

    public function getIterator(): Traversable
    {

        foreach ($this->getOffsets() as $offset) {
            yield from $this->yieldOffset($offset);
        }
    }

    public function getReverseIterator(): Traversable
    {
        foreach (array_reverse($this->getOffsets()) as $offset) {
            yield from $this->yieldOffset($offset);
        }
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function count(): int
    {
        return count($this->getOffsets());
    }

}
