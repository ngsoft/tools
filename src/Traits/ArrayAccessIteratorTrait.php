<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Traits;

trait ArrayAccessIteratorTrait {

    /** @var array */
    protected $storage = [];

    /**
     * {@inheritdoc}
     * @suppress PhanUndeclaredMethod
     */
    public function current() {
        $key = $this->key();
        if ($key === null) return false;
        return $this->offsetGet($key);
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
