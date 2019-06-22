<?php

namespace NGSOFT\Tools\Traits;

use ArrayIterator;

trait ArrayAccessTrait {

    /** @var array */
    protected $storage;

    /** {@inheritdoc} */
    public function offsetExists($offset) {
        return array_key_exists($offset, $this->storage);
    }

    /** {@inheritdoc} */
    public function offsetGet($offset) {
        return $this->storage[$offset] ?? null;
    }

    /** {@inheritdoc} */
    public function offsetSet($offset, $value) {
        if ($offset === null) $this->storage[] = $value;
        else $this->storage[$offset] = $value;
    }

    /** {@inheritdoc} */
    public function offsetUnset($offset) {
        unset($this->storage[$offset]);
    }

    /** {@inheritdoc} */
    public function count(): int {
        return count($this->storage);
    }

    /** {@inheritdoc} */
    public function getIterator() {
        return new ArrayIterator($this->storage);
    }

}
