<?php

namespace NGSOFT\Tools\Objects;

class Params extends SimpleObject {

    /**
     * {@inheritdoc}
     * @throws BadMethodCallException
     */
    public function sort(callable $callback) {
        throw new \BadMethodCallException('Params cannot be sorted.');
    }

    public function import(array $attributes) {
        $this->storage = [];
        foreach ($attributes as $k => $v) {
            $this->offsetSet($k, $v);
        }
    }

    public function export(): array {
        return $this->storage;
    }

    public function offsetSet($k, $v) {
        if (is_array($v)) {
            if (is_int(key($v))) {
                return parent::offsetSet($k, new Collection($v));
            }
            return parent::offsetSet($k, new Params($v));
        }
        parent::offsetSet($k, $v);
    }

    public function &__get($k) {
        $return = null;
        if ($this->offsetExists($k)) {
            $return = &$this->storage[$k];
        }
        return $return;
    }

    public function __isset($k) {
        return $this->offsetExists($k);
    }

    public function __set($k, $v) {
        $this->offsetSet($k, $v);
    }

    public function __unset($k) {
        $this->offsetUnset($k);
    }

}
