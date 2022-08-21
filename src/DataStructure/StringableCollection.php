<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use IteratorAggregate,
    LogicException,
    NGSOFT\Traits\CloneWith,
    Stringable,
    Traversable;

/**
 * A Stringable collection of stringable
 *
 */
class StringableCollection implements Stringable, IteratorAggregate
{

    use CloneWith,
        CollectionTrait;

    /** @var Stringable[] */
    protected array $storage = [];
    protected ?string $cache = null;

    /**
     * Append value to the stack
     */
    public function append(string|Stringable|int|float ...$stringables): static
    {
        $this->cache = null;

        foreach ($stringables as $stringable) {

            if ($this === $stringable) {
                throw new LogicException(sprintf('Cannot %s the same instance of %s into itself(infinite recursion).', __FUNCTION__, static::class));
            }


            if ($stringable instanceof Stringable === false) {
                $stringable = new Text($stringable);
            }

            $this->storage[] = $stringable;
        }


        return $this;
    }

    /**
     * Alias of append
     */
    public function push(string|Stringable|int|float ...$stringables): static
    {
        return $this->append(...$stringables);
    }

    /**
     * Pops and returns the last element
     */
    public function pop(): ?Stringable
    {
        $this->cache = null;
        return array_pop($this->storage);
    }

    /**
     * @phan-suppress PhanUnusedProtectedMethodParameter
     */
    protected function _append(mixed $offset, mixed $value): void
    {
        $this->storage[] = $value;
    }

    protected function createNew(): static
    {
        return new static();
    }

    public function entries(Sort $sort = Sort::ASC): iterable
    {

        $keys = array_keys($this->storage);

        if ($sort->is(Sort::DESC)) {
            $keys = array_reverse($keys);
        }

        foreach ($keys as $offset) {
            yield $this->storage[$offset];
        }
    }

    public function getIterator(): Traversable
    {
        yield from $this->entries();
    }

    protected function build(): string
    {

        $result = '';

        foreach ($this as $stringable) {
            $result .= (string) $stringable;
        }

        return $result;
    }

    public function __toString(): string
    {
        return $this->cache ??= $this->build();
    }

}
