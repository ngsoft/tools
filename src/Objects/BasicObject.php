<?php

namespace NGSOFT\Tools\Objects;

class BasicObject extends JSArray {

    public function toArray(): array {
        $arr = parent::toArray();

        foreach ($arr as &$value) {
            if ($value instanceof static) $value = $value->toArray();
        }
        return $arr;
    }

    /** {@inheritdoc} */
    protected function loadArray(array $array) {
        $this->storage = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) $this->storage[$key] = new static($value);
            elseif ($value instanceof static) $this->storage[$key] = clone $value;
            else $this->storage[$key] = $value;
        }
    }

    /** {@inheritdoc} */
    public function &offsetGet($offset) {
        $value = null;
        if ($this->offsetExists($offset)) $value = &$this->storage[$offset];
        elseif ($offset === null) {
            $value = new static();
            $this->storage[] = $value;
        }
        return $value;
    }

    /** {@inheritdoc} */
    public function offsetSet($offset, $value) {
        if (is_array($value)) $value = new static($value);
        parent::offsetSet($offset, $value);
    }

    /** {@inheritdoc} */
    public function &__get($prop) {
        $val = null;
        if ($this->offsetExists($prop)) $val = &$this->storage[$prop];
        return $val;
    }

    /** {@inheritdoc} */
    public function __set($prop, $value) {
        $this->offsetSet($prop, $value);
    }

    /** {@inheritdoc} */
    public function __isset($prop) {
        return $this->offsetExists($prop);
    }

    /** {@inheritdoc} */
    public function __invoke(array $array) {
        $this->loadArray($array);
        return $this;
    }

    /** {@inheritdoc} */
    public function __clone() {

        foreach ($this->storage as &$value) {
            if ($value instanceof static) $value = clone $value;
        }
    }

}
