<?php

declare(strict_types=1);

namespace NGSOFT\DataStructure;

use Countable,
    IteratorAggregate,
    JsonSerializable,
    LogicException,
    NGSOFT\Tools,
    Stringable,
    Traversable,
    ValueError;
use function get_debug_type,
             mb_strlen;

/**
 * A Stringable collection of stringable
 *
 */
class StringableCollection implements Stringable, IteratorAggregate, JsonSerializable, Countable
{

    use CollectionTrait;

    /** @var Stringable[] */
    protected array $storage = [];
    protected ?string $cache = null;

    public static function create(string|Stringable|int|float ...$stringables): static
    {
        return new static(...$stringables);
    }

    public function __construct(
            string|Stringable|int|float ...$stringables
    )
    {
        $this->append(...$stringables);
    }

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
     * Insert new values beforre the existing ones
     */
    public function prepend(string|Stringable|int|float ...$stringables): static
    {

        $this->cache = null;

        $new = [];
        foreach ($stringables as $stringable) {

            if ($this === $stringable) {
                throw new LogicException(sprintf('Cannot %s the same instance of %s into itself(infinite recursion).', __FUNCTION__, static::class));
            }


            if ($stringable instanceof Stringable === false) {
                $stringable = new Text($stringable);
            }

            $new[] = $stringable;
        }

        array_unshift($this->storage, ...$new);

        return $this;
    }

    /**
     * Alias of prepend
     */
    public function unshift(string|Stringable|int|float ...$stringables): static
    {
        return $this->prepend(...$stringables);
    }

    /**
     * Removes and returns the first element
     */
    public function shift(): ?Stringable
    {
        $this->cache = null;
        return array_shift($this->storage);
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
     * Returns a new  collection using a delimitter
     */
    public function split(string $separator): static
    {
        $method = preg_valid($separator) ? 'preg_split' : 'explode';

        return new static(...$method($separator, (string) $this));
    }

    /**
     * Joins the stringable in the collection and returns the result as a string
     */
    public function join(string $separator): string
    {
        return implode($separator, Tools::map(fn($string) => $string, $this->entries()));
    }

    /**
     * map and filter test
     */
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

        return $this->join('');
    }

    public function jsonSerialize(): mixed
    {
        return (string) $this;
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function count(): int
    {
        return mb_strlen((string) $this);
    }

    public function __toString(): string
    {
        return $this->cache ??= $this->build();
    }

    public function __serialize(): array
    {
        return $this->storage;
    }

    public function __unserialize(array $data)
    {
        $this->storage = $data;
    }

}
