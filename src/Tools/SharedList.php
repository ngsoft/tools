<?php

declare(strict_types=1);

namespace NGSOFT\Tools;

use ArrayAccess,
    Countable,
    Generator,
    IteratorAggregate,
    JsonSerializable,
    NGSOFT\Traits\Exportable,
    OutOfBoundsException,
    OverflowException,
    Stringable,
    UnexpectedValueException;
use function get_debug_type;

/**
 * Simulates Many-To-Many relations found in database using strings
 * Uses 2 Sets One of Key and one of Value
 * Keys can have multiple values and values can have multiple keys
 *
 * Uses a similar API as JS Map(), but works differently
 *
 * @link https://en.wikipedia.org/wiki/Many-to-many_(data_model)
 * @link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Map
 *
 *
 */
final class SharedList implements Countable, IteratorAggregate, JsonSerializable, Stringable, ArrayAccess {

    use Exportable;

    /**
     * Links the key and value Set indexes between them (Associative entity)
     * @link https://en.wikipedia.org/wiki/Associative_entity
     * @var array
     */
    private $pairs = [/* $pairIndex => [$keyIndex, $valueIndex] */];

    /** @var Set */
    private $keys; /* $keyIndex => 'Key to assign.' */

    /** @var Set */
    private $values; /* $valueIndex => 'Value that can have many keys.' */

    ////////////////////////////   Initialization   ////////////////////////////

    /**
     * Creates a new SharedList
     * @return static
     */
    public static function create(): self {
        return new static();
    }

    public function __construct() {
        $this->clear();
    }

    ////////////////////////////   API   ////////////////////////////

    /**
     * Reinitialize the instance
     *
     * @return void
     */
    public function clear(): void {
        $this->keys = new Set();
        $this->values = new Set();
        $this->pairs = [];
    }

    /**
     * Checks if an association exists
     *
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function has(string $key, string $value): bool {
        $keyIndex = $this->keys->indexOf($key);
        $valueIndex = $this->values->indexOf($value);
        return
                $keyIndex != -1 and
                $valueIndex != -1 and
                in_array([$keyIndex, $valueIndex], $this->pairs);
    }

    /**
     * Set an association between key and value
     * If they do not exists, they will be created
     *
     * @param string $key
     * @param string $value
     * @return self
     */
    public function set(string $key, string $value): self {
        if (!$this->has($key, $value)) {
            $k = $this->keys->add($key); //do not add if already exists, but gets the index
            $v = $this->values->add($value);
            $this->pairs[] = [$k, $v];
        }
        return $this;
    }

    /**
     * Removes an association between key and value
     *
     * @param string $key the key
     * @param string|null $value if set to null all values will be unassociated from the key
     * @return self
     */
    public function delete(string $key, string $value = null): self {
        if (
                !is_string($value) and
                ($keyid = $this->keys->indexOf($key)) != -1
        ) {
            $this->pairs = array_filter($this->pairs, fn($p) => $p[0] != $keyid);
        } elseif (is_string($value)) {
            unset($this->pairs[$this->indexOf($key, $value)]);
            // enforces value removal if not paired
            if ($this->countValuesPairs($value) == 0) $this->values->delete($value);
        }
        //enforces key removal if not paired
        if ($this->countKeysPairs($key) == 0) $this->keys->delete($key);
        return $this;
    }

    /**
     * Returns all the associated values for a key
     *
     * @param string $key
     * @return string[]
     */
    public function get(string $key): array {
        $result = [];
        //do not loop if not necessary
        if ($this->keys->has($key)) {
            foreach ($this->entries() as $k => $value) {
                if ($key === $k) $result[] = $value;
            }
        }
        return $result;
    }

    /**
     * Returns all the associated keys for a value
     * get() reverse lookup
     *
     * @param string $value
     * @return string[]
     */
    public function getKeys(string $value): array {
        $result = [];
        //do not loop if not necessary
        if ($this->values->has($value)) {
            foreach ($this->entries() as $key => $v) {
                if ($value === $v) $result[] = $key;
            }
        }
        return $result;
    }

    /**
     * The forEach() method executes a provided function once per each key/value pair
     *
     * @param callable $callable callable that takes 2 arguments (string $key, string $value)
     * @return void
     */
    public function forEach(callable $callable): void {
        foreach ($this->entries() as $key => $value) {
            $callable($value, $key);
        }
    }

    /**
     * The keys() method returns a new Iterator object that contains the keys
     * to get an array use builtin iterator_to_array()
     *
     * @return \Generator<string>
     */
    public function keys(): Generator {
        yield from $this->keys;
    }

    /**
     * The values() method returns a new Iterator object that contains the values
     * to get an array use builtin iterator_to_array()
     *
     * @return Generator<string>
     */
    public function values(): Generator {
        yield from $this->values;
    }

    /**
     * The entries() method returns a new Iterator object that contains the [key, value] pairs
     *
     * @return \Generator<string,string>
     */
    public function entries(): Generator {
        foreach ($this->pairs as list($k, $v)) {
            yield $this->keys[$k] => $this->values[$v];
        }
    }

    ////////////////////////////   Utils   ////////////////////////////

    /**
     * Exports the pairs as an array
     *
     * @return array
     */
    public function toArray(): array {
        return $this->export();
    }

    /**
     * Returns a new instance with filtered results using a callable
     * Iterates all the pairs
     *
     * @param callable $callable callable must returns a boolean and takes 2 arguments (string $value, string $key)
     * @return self
     */
    public function filter(callable $callable): self {
        $i = new static();
        foreach ($this->entries() as $key => $value) {
            $retval = $callable($value, $key); //$value,$key to keep consistency with other methods
            $this->assertBool($retval);
            if ($retval === true) $i->set($key, $value);
        }
        return $i;
    }

    /**
     * Returns a new instance with pairs from other SharedList added to the current
     *
     * @param SharedList ...$sharedLists any number of shared lists to merge
     * @return self
     */
    public function concat(SharedList ...$sharedLists): self {

        $i = new static();
        // add current pairs to the new instance
        // we do not clone to create new indexes for the sets (if previously removed keys or values)
        foreach ($this->entries() as $key => $value) {
            $i->set($key, $value);
        }
        //now we add the other instances if any
        foreach ($sharedLists as $list) {
            foreach ($list->entries() as $key => $value) {
                $i->set($key, $value);
            }
        }
        return $i;
    }

    /**
     * Tests if at least one of the elements from the SharedList pass the test implemented by the callable
     *
     * @param callable $callable callable must returns a boolean and takes 2 arguments (string $value, string $key)
     * @return bool
     */
    public function some(callable $callable): bool {

        foreach ($this->entries() as $key => $value) {
            $retval = $callable($value, $key);
            $this->assertBool($retval);
            if (true === $retval) return true;
        }
        return false;
    }

    /**
     * Tests if all the elements from the SharedList pass the test implemented by the callable
     *
     * @param callable $callable callable must returns a boolean and takes 2 arguments (string $value, string $key)
     * @return bool
     */
    public function every(callable $callable): bool {
        foreach ($this->entries() as $key => $value) {
            $retval = $callable($value, $key);
            $this->assertBool($value);
            if (false === $retval) return false;
        }
        return true;
    }

    ////////////////////////////   Helpers   ////////////////////////////

    /**
     * Get the index of the association between key and value
     *
     * @internal private Set to public as it can have some uses.
     * @param string $key
     * @param string $value
     * @return int
     */
    public function indexOf(string $key, string $value): int {
        $k = $this->keys->indexOf($key);
        $v = $this->values->indexOf($value);
        $id = array_search([$k, $v], $this->pairs); // if a previous value is -1 it will not be found
        return $id === false ? -1 : $id;
    }

    /**
     * Used to check a callable return value
     *
     * @link https://www.php.net/manual/en/class.unexpectedvalueexception.php
     * @param mixed $value
     * @return void
     * @throws UnexpectedValueException
     */
    private function assertBool($value): void {
        if (!is_bool($value)) {
            throw new UnexpectedValueException(sprintf('Invalid return value for callable, bool expected, %s given.', get_debug_type($value)));
        }
    }

    /**
     * Count the number of pairs a key is assigned
     * helps enforce that an orphaned key is removed from the set
     *
     * @param string $key
     * @return int
     */
    private function countKeysPairs(string $key): int {
        $cnt = 0;
        if (($keyId = $this->keys->indexOf($key)) != -1) {
            foreach ($this->pairs as list($k)) {
                if ($keyId === $k) $cnt++;
            }
        }
        return $cnt;
    }

    /**
     * Count the number of pairs a value is assigned
     * helps enforce that an orphaned value is removed from the set
     *
     * @param string $value
     * @return int
     */
    private function countValuesPairs(string $value): int {
        $cnt = 0;
        if (($valueId = $this->values->indexOf($value)) != -1) {
            foreach ($this->pairs as list(, $v)) {
                if ($valueId === $v) $cnt++;
            }
        }
        return $cnt;
    }

    ////////////////////////////   Interfaces   ////////////////////////////

    /** {@inheritdoc} */
    public function offsetExists(mixed $offset): bool {
        if (!is_string($offset)) throw new OutOfBoundsException();
        return $this->keys->has($offset);
    }

    /** {@inheritdoc} */
    public function offsetGet(mixed $offset): mixed {
        if (!is_string($offset)) throw new OutOfBoundsException();
        if ($this->offsetExists($offset)) return $this->get($offset);
        return null;
    }

    /**
     * {@inheritdoc}
     * @suppress PhanUnusedPublicFinalMethodParameter
     */
    public function offsetSet(mixed $offset, mixed $value): void {
        throw new OverflowException();
    }

    /** {@inheritdoc} */
    public function offsetUnset(mixed $offset): void {
        if (!is_string($offset)) throw new OutOfBoundsException();
        $this->delete($offset);
    }

    /** {@inheritdoc} */
    public function count(): int {
        return count($this->pairs);
    }

    /** {@inheritdoc} */
    public function getIterator(): \Traversable {
        yield from $this->entries();
    }

    /** {@inheritdoc} */
    protected function export(): array {
        $result = [];
        foreach ($this->pairs as list($k, $v)) {
            $result[] = [$this->keys[$k] => $this->values[$v]];
        }
        return $result;
    }

    /** {@inheritdoc} */
    protected function import(array $array): void {
        $this->clear();
        $this->extract($array);
    }

    /** {@inheritdoc} */
    public function __serialize() {
        return $this->compact('pairs', 'keys', 'values');
    }

    /** {@inheritdoc} */
    public function __clone() {
        $this->keys = clone $this->keys;
        $this->values = clone $this->values;
    }

}
