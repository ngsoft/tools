<?php

declare(strict_types=1);

namespace NGSOFT\Tools;

use ArrayAccess,
    Countable,
    InvalidArgumentException,
    IteratorAggregate,
    JsonSerializable;
use NGSOFT\Traits\{
    ArrayAccessEssentials, Exportable, PropertyAccess
};
use Stringable;
use function get_debug_type;

/**
 * A Fixed Array
 * An array with fixed capacity
 * Uses LRU model (Last Recently Used gets removed first)
 *
 * @property-read int $length Get the current Length
 * @property int $capacity The current capacity
 * @property bool $recursive Tells if Sub-arrays are FixedArrays
 *
 */
final class FixedArray implements ArrayAccess, Countable, IteratorAggregate, Stringable, JsonSerializable {

    use Exportable;
    use PropertyAccess;
    use ArrayAccessEssentials;

    /**
     * The default capacity
     */
    public const DEFAULT_CAPACITY = 8;

    /** @var array */
    protected $storage = [];

    /** @var bool */
    private $recursive = true;

    /** @var int */
    private $capacity = self::DEFAULT_CAPACITY;

    ////////////////////////////   Initialization   ////////////////////////////

    /**
     * Instanciate a new FixedArray
     * @param int $capacity
     * @return self
     */
    public static function create(int $capacity = self::DEFAULT_CAPACITY): self {
        return new static($capacity);
    }

    /**
     * Creates a new Fixed array using an array
     *
     * @param array $array
     * @param int|null $capacity if set to null will take the length of the array or the default capacity, the one that is larger
     * @return self
     */
    public static function from(array $array, int $capacity = null): self {
        $capacity = $capacity ?? count($array);
        $i = new static($capacity);
        $i->storage = $array;
        $i->enforceCapacity();
        return $i;
    }

    /**
     * @param int $capacity
     */
    public function __construct(int $capacity = self::DEFAULT_CAPACITY) {
        $this->setCapacity($capacity);
    }

    ////////////////////////////   API   ////////////////////////////

    /**
     * Exports to regular array
     *
     * @return array
     */
    public function toArray(): array {
        return $this->export();
    }

    /**
     * Get the current Length
     * @return int
     */
    public function getLength(): int {
        return $this->count();
    }

    /**
     * Get the current assigned capacity
     *
     * @return int
     */
    public function getCapacity(): int {
        return $this->capacity;
    }

    /**
     * Set the capacity
     * Capacity cannot be decreased
     *
     * @param int $capacity the new capacity
     * @return void
     */
    public function setCapacity(int $capacity): void {
        $this->capacity = max($this->capacity, $capacity);
    }

    /**
     * Get the current recursion value
     *
     * @return bool
     */
    public function getRecursive(): bool {
        return $this->recursive;
    }

    /**
     * Set the recursion value
     *
     * @param bool $recursive
     * @return void
     */
    public function setRecursive(bool $recursive): void {
        $this->recursive = $recursive;
    }

    /**
     * Concat multiples iterables with the current storage and returns a copy
     *
     * @param iterable ...$objects
     * @return static
     */
    public function concat(iterable ...$objects): self {
        $result = clone $this;
        $result->clear();
        foreach ($this->storage as $key => $value) {
            $result->storage[$key] = $value;
        }
        foreach ($objects as $obj) {
            $array = $this->iterableToArray($obj);
            $result->storage = $result->merge($result->storage, $array);
        }
        $result->enforceCapacity();
        return $result;
    }

    ////////////////////////////   ToolBox   ////////////////////////////

    /**
     * Enforces Max capacity
     */
    private function enforceCapacity() {
        if (count($this->storage) > $this->capacity) {
            foreach (array_keys($this->storage)as $id) {
                if ($this->capacity >= count($this->storage)) break;
                unset($this->storage[$id]);
            }
        }
        if ($this->recursive) {
            $obj = new static($this->capacity);
            foreach ($this->storage as $id => $value) {
                if (is_array($value)) {
                    $obj->clear();
                    //keeps the capacity (recursively)
                    $obj->storage = $value;
                    $obj->enforceCapacity();
                    $this->storage[$id] = $obj->storage;
                }
            }
        }
    }

    /**
     * Append a value to the array
     * This enforces the Last Recently Used gets removed first
     *
     * @param int|string|null $offset
     * @param mixed $value
     * @return int|string the current offset
     */
    protected function append($offset, $value) {
        if (null === $offset) {
            $offset = -1;
            foreach (array_keys($this->storage) as $id) {
                if (is_int($id) and $id > $offset) $offset = $id;
            }
            $offset++;
        }
        if (
                !is_int($offset) and
                !is_string($offset)
        ) {
            throw new InvalidArgumentException(sprintf('Invalid $offset value int|string requested, %s given.', get_debug_type($offset)));
        }
        if (array_key_exists($offset, $this->storage)) unset($this->storage[$offset]);
        $this->storage[$offset] = $value; //put it in the last position
        $this->enforceCapacity();
        return $offset;
    }

    ////////////////////////////   Interfaces   ////////////////////////////

    /** {@inheritdoc} */
    public function &offsetGet($offset) {
        $null = null;
        if ($offset === $null) $offset = $this->append($offset, []);
        if (!$this->offsetExists($offset)) return $null;
        //update last access
        $this->append($offset, $this->storage[$offset]);
        if (is_array($this->storage[$offset])) {
            if ($this->recursive) {
                $obj = clone $this;
                $obj->clear();
                $obj->storage = &$this->storage[$offset];
                return $obj;
            } else $obj = &$this->storage[$offset];
            return $obj;
        }
        return $this->storage[$offset];
    }

    /**
     * Alternative way to retrieved cached data using var_export to export variables instead of serialize
     *
     * @param array $array
     */
    public static function __set_state($array) {
        $i = new static();
        $i->import($array);
        return $i;
    }

    /** {@inheritdoc} */
    protected function export(): array {
        return $this->storage;
    }

    /** {@inheritdoc} */
    public function __serialize() {
        return $this->compact('storage', 'capacity', 'recursive');
    }

    /** {@inheritdoc} */
    protected function import(array $array): void {
        $this->extract($array);
        $this->enforceCapacity();
    }

}
