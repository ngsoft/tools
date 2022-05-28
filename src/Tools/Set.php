<?php

declare(strict_types=1);

namespace NGSOFT\Tools;

use ArrayAccess,
    Countable,
    Generator,
    InvalidArgumentException,
    IteratorAggregate,
    JsonSerializable,
    LogicException,
    NGSOFT\Traits\Exportable,
    Stringable;
use function get_debug_type;

/**
 * A collection of unique strings
 * A removed entry will not reallocate its index
 *
 * Same api as JS Set() with indexes as we need them, the difference is you can only add strings
 * @link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Set
 *
 */
class Set implements Countable, IteratorAggregate, JsonSerializable, ArrayAccess, Stringable {

    use Exportable;

    /** @var string[] */
    private $storage = [];

    /**
     * Keep the last added key index
     * increments when a value is added
     * @var int
     */
    private $lastIndex = -1;

    ////////////////////////////   API   ////////////////////////////

    /**
     * Removes all elements and reset the index
     * @return void
     */
    public function clear(): void {

        $this->storage = [];
        $this->lastIndex = -1;
    }

    /**
     * Append a string in the set if it doesn't exists
     *
     * @param string $value
     * @return int the value index
     */
    public function add(string $value): int {
        if (empty($value)) throw new InvalidArgumentException('Value cannot be empty.');
        if (!$this->has($value)) {
            $this->lastIndex++;
            $this->storage[$this->lastIndex] = $value;
        }
        return $this->indexOf($value);
    }

    /**
     * Removes a value from the set
     *
     * @param string $value
     * @return void
     */
    public function delete(string $value): void {
        $index = $this->indexOf($value);
        if ($index != -1) unset($this->storage[$index]);
    }

    /**
     * Checks if a string exists in the collection
     * @param string $value
     * @return bool
     */
    public function has(string $value): bool {
        return in_array($value, $this->storage);
    }

    /**
     * Returns the string index
     * @param string $value
     * @return int the index in the collection, -1 if not found
     */
    public function indexOf(string $value): int {
        $id = array_search($value, $this->storage);
        return $id === false ? -1 : $id;
    }

    /**
     * Returns a new iterator indexed by id
     * not the same as JS as we need the index some times
     *
     * @return \Generator<int,string>
     */
    public function entries(): Generator {
        foreach ($this->storage as $id => $value) {
            yield $id => $value;
        }
    }

    /**
     * Returns a new iterator with only the values
     * @return \Generator<string>
     */
    public function values(): Generator {
        foreach ($this->storage as $value) {
            yield $value;
        }
    }

    /**
     * Returns a new iterator with only the indexes
     * @return \Generator<int>
     */
    public function keys(): Generator {
        foreach (array_keys($this->storage) as $index) {
            yield $index;
        }
    }

    /**
     * The forEach() method executes a provided function once for each value in the Set
     *
     * @param callable $callable callable that takes 2 arguments: (string $value, int $index)
     * @return void
     */
    public function forEach(callable $callable): void {
        foreach ($this->entries() as $index => $value) {
            $callable($value, $index);
        }
    }

    /**
     * Exports the set as an array
     *
     * @return array
     */
    public function toArray(): array {
        return $this->storage;
    }

    ////////////////////////////   Interfaces   ////////////////////////////

    /** {@inheritdoc} */
    public function count(): int {
        return count($this->storage);
    }

    /** {@inheritdoc} */
    protected function export(): array {
        return $this->storage;
    }

    /** {@inheritdoc} */
    public function __serialize(): array {
        return $this->compact('storage', 'lastIndex');
    }

    /** {@inheritdoc} */
    protected function import(array $array): void {
        $this->clear();
        $this->extract($array);
    }

    /**
     * @return \Generator<int,string>
     */
    public function getIterator(): \Traversable {
        yield from $this->entries();
    }

    /** {@inheritdoc} */
    public function offsetExists(mixed $offset): bool {
        return
                is_int($offset) and
                isset($this->storage[$offset]);
    }

    /** {@inheritdoc} */
    public function offsetGet(mixed $offset): mixed {
        return $this->storage[$offset] ?? null;
    }

    /** {@inheritdoc} */
    public function offsetSet(mixed $offset, mixed $value): void {
        if (is_string($offset) or is_int($offset)) throw new LogicException(sprintf('Invalid offset %s, data can be added, not replaced', (string) $offset));
        elseif (!is_string($value)) throw new InvalidArgumentException(sprintf('Invalid value type: string requested, %s given.', get_debug_type($value)));
        elseif (is_null($offset)) $this->add($value);
    }

    /** {@inheritdoc} */
    public function offsetUnset($offset): void {
        unset($this->storage[$offset]);
    }

}
