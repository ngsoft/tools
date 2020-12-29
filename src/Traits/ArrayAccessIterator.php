<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Traits;

trait ArrayAccessIterator {

    /** @var array */
    protected $storage = [];

    /**
     * {@inheritdoc}
     * @suppress PhanUndeclaredMethod
     */
    public function current() {
        if (!$this->valid()) return null;
        $key = $this->key();
        if ($this instanceof \ArrayAccess) {
            return $this->offsetGet($key);
        } else return $this->storage[$key];
    }

    /** {@inheritdoc} */
    public function key() {
        return key($this->storage);
    }

    /** {@inheritdoc} */
    public function next() {
        next($this->storage);
    }

    /** {@inheritdoc} */
    public function rewind() {
        reset($this->storage);
    }

    /** {@inheritdoc} */
    public function valid() {
        return $this->key() !== null;
    }

}
