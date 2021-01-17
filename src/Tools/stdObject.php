<?php

declare(strict_types=1);

namespace NGSOFT\Tools;

use NGSOFT\Tools;
use function mb_strpos;

/**
 * stdObject A Base Array Like Object
 * Can also access keys using dot notation
 */
class stdObject extends SimpleObject {

    const VERSION = Tools::VERSION;

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
        $offset = trim($offset, '.');
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
        $offset = trim($offset, '.');
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
            $instance->clear();
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
            $offset = trim($offset, '.');
            $result = &$this->storage;
            $params = explode('.', $offset);
            foreach ($params as $param) {
                if (!array_key_exists($param, $result)) $result[$param] = [];
                $result = &$result[$param];
            }
            $result = $value;
        }
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
            $offset = trim($offset, '.');
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
