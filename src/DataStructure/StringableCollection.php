<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use IteratorAggregate,
    LogicException,
    NGSOFT\Traits\CloneWith,
    Stringable,
    Traversable,
    ValueError;
use function get_debug_type;

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

    protected function test(Stringable $value, string $string, callable $callback): bool|Stringable
    {


        $result = $callback($string, $value);

        if (is_bool($result)) {
            return $result;
        }

        if ($result === $string || $result === $value || $result === null) {
            return $value;
        }

        if (is_scalar($result)) {
            return new Text($result);
        }

        if ($result instanceof Stringable === false) {
            throw new ValueError(sprintf('Invalid return type %s for callback, not an instance of %s', get_debug_type($result), Stringable::class));
        }

        return $result;
    }

    /**
     * Applies the callback to the elements of the storage and returns a copy
     */
    public function map(callable $callback): static
    {
        $result = new static();

        foreach ($this->entries() as $_value => $_str) {

            $value = $this->test($_value, $_str, $callback);

            if (is_bool($value)) {
                throw new ValueError('Invalid return type bool for callable');
            }

            $result->append($value);
        }

        return $result;
    }

    /**
     * Returns a copy with all the elements that passes the test
     */
    public function filter(callable $callback): static
    {
        $result = new static();

        foreach ($this->entries() as $value => $str) {

            if (false === $this->test($value, $str, $callback)) {
                continue;
            }
            $result->append($value);
        }

        return $result;
    }

    public function entries(Sort $sort = Sort::ASC): iterable
    {

        $keys = array_keys($this->storage);

        if ($sort->is(Sort::DESC)) {
            $keys = array_reverse($keys);
        }

        foreach ($keys as $offset) {
            $value = $this->storage[$offset];
            yield $value => (string) $value;
        }
    }

    public function getIterator(): Traversable
    {
        yield from $this->storage;
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
