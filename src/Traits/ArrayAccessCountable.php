<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

use Generator,
    InvalidArgumentException;
use function get_debug_type;

trait ArrayAccessCountable {

    /** @var array */
    protected $storage = [];

    ////////////////////////////   Utils   ////////////////////////////

    /**
     * Clears the storage
     *
     * @return void
     */
    public function clear(): void {
        $array = [];
        $this->storage = &$array;
    }

    /**
     * Appends a value to the storage
     *
     * @param int|string|null $offset
     * @param mixed $value
     * @return int|string The Offset
     * @throws InvalidArgumentException
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

        $this->storage[$offset] = $value;
        return $offset;
    }

    ////////////////////////////   ArrayAccess   ////////////////////////////

    /** {@inheritdoc} */
    public function offsetExists($offset) {
        return
                array_key_exists($offset, $this->storage) and
                null !== $this->storage[$offset];
    }

    /** {@inheritdoc} */
    public function &offsetGet($offset) {
        $null = null;
        if ($offset === $null) $offset = $this->append($offset, []);
        if (!$this->offsetExists($offset)) return $null;
        if (is_array($this->storage[$offset])) {
            $instance = clone $this;
            $instance->clear();
            $instance->storage = &$this->storage[$offset];
            return $instance;
        }
        return $this->storage[$offset];
    }

    /** {@inheritdoc} */
    public function offsetSet($offset, $value) {
        if ($value instanceof self) {
            $value = $value->storage;
        }
        $this->append($offset, $value);
    }

    /** {@inheritdoc} */
    public function offsetUnset($offset) {
        unset($this->storage[$offset]);
    }

    ////////////////////////////   Countable   ////////////////////////////

    /** {@inheritdoc} */
    public function count() {
        return count($this->storage);
    }

    ////////////////////////////   Iterator   ////////////////////////////

    /**
     * @return Generator
     */
    public function getIterator() {
        foreach (array_keys($this->storage) as $id) {
            yield $id => $this->offsetGet($id);
        }
    }

}
