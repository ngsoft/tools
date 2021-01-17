<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Objects;

use function mb_strpos;

/**
 * stdObject A Base Array Like Object
 * Can also access keys using dot notation
 */
class stdObject extends SimpleObject {

    const VERSION = \NGSOFT\Tools\VERSION;

    /** @var bool */
    protected $dotArrayConvertion = true;

    ////////////////////////////   Configure   ////////////////////////////

    /**
     * Disable dot properties convertions to stdObject
     * @return static
     */
    public function disableDotArrayConvertion(): self {
        $this->dotArrayConvertion = false;
        return $this;
    }

    ////////////////////////////   Overrides   ////////////////////////////

    /** {@inheritdoc} */
    public function offsetExists($offset) {
        if (
                is_int($offset)
                or is_null($offset)
                or(mb_strpos($offset, '.') === false)
        ) return parent::offsetExists($offset);

        $array = $this->storage;
        $params = explode('.', $offset);
        foreach ($params as $prop) {
            if (is_array($array)) {
                if (array_key_exists($prop, $array)) {
                    $array = $array[$prop];
                } else return false;
            }
        }
        return true;
    }

    /** {@inheritdoc} */
    public function &offsetGet($offset) {
        if (
                is_int($offset)
                or is_null($offset)
                or(mb_strpos($offset, '.') === false)
        ) {
            $value = parent::offsetGet($offset);
            return $value;
        } elseif (!$this->offsetExists($offset)) {
            $value = null;
            return $value;
        }

        $result = &$this->storage;
        $params = explode('.', $offset);
        foreach ($params as $param) {
            $result = &$result[$param];
        }
        if (
                is_array($result)
                and $this->dotArrayConvertion === true
        ) {
            $instance = clone $this;
            $instance->storage = &$result;
            return $instance;
        }
        return $result;
    }

    /** {@inheritdoc} */
    public function offsetSet($offset, $value) {
        if (
                is_int($offset)
                or is_null($offset)
                or(mb_strpos($offset, '.') === false)
        ) parent::offsetSet($offset, $value);
        else {
            if ($value instanceof self) {
                $value = $value->storage;
            }
            $array = [];
            $result = &$array;
            $params = explode('.', $offset);
            $last = array_key_last($params);
            foreach ($params as $index => $param) {
                $result[$param] = [];
                $result = &$result[$param];
                if ($index === $last) $result[$param] = $value;
            }
            $result = $value;

            $this->concat($array);
        }


        // parent::offsetSet($offset, $value);
    }

    /** {@inheritdoc} */
    public function offsetUnset($offset) {
        if (
                is_int($offset)
                or is_null($offset)
                or(mb_strpos($offset, '.') === false)
        ) parent::offsetUnset($offset);
        elseif ($this->offsetExists($offset)) {
            $array = &$this->storage;
            $params = explode('.', $offset);
            $last = array_key_last($params);
            foreach ($params as $index => $param) {
                if ($index < $last) {
                    $array = &$array[$param];
                    continue;
                }
                unset($array[$param]);
                break;
            }
        }
    }

}
