<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Traits;

trait ArrayAccessCountable {

    /** @var array */
    protected $storage = [];

    ////////////////////////////   Utils   ////////////////////////////

    /**
     * Convert iterable to array
     * @param iterable $obj
     * @return array
     */
    protected function iterableToArray(iterable $obj): array {
        $result = [];
        foreach ($obj as $key => $value) {
            if (
                    is_iterable($value)
                    and ($value instanceof self or is_array($value))
            ) {
                $result[$key] = $this->iterableToArray($value);
            } else $result[$key] = $value;
        }
        return $result;
    }

    ////////////////////////////   ArrayAccess   ////////////////////////////

    /** {@inheritdoc} */
    public function offsetExists($offset) {
        return array_key_exists($offset, $this->storage);
    }

    /** {@inheritdoc} */
    public function &offsetGet($offset) {
        $value = null;
        if ($offset === null) {
            $this->storage[] = [];
            $offset = array_key_last($this->storage);
        }
        if (!$this->offsetExists($offset)) return $value;
        if (is_array($this->storage[$offset])) {
            //link sub arrays
            $value = &$this->storage[$offset];
            $instance = clone $this;
            $instance->storage = &$value;
            return $instance;
        } else $value = $this->storage[$offset];
        return $value;
    }

    /** {@inheritdoc} */
    public function offsetSet($offset, $value) {
        if ($value instanceof self) {
            $value = $this->iterableToArray($value->storage);
        }
        if ($offset === null) $this->storage[] = $value;
        else $this->storage[$offset] = $value;
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

    ////////////////////////////   JsonSerializable   ////////////////////////////

    /** {@inheritdoc} */
    public function jsonSerialize() {
        return $this->storage;
    }

}
