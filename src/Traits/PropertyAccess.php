<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

use NGSOFT\Tools,
    TypeError;
use function get_debug_type;

/**
 * Uses camelCased getters and setters to translate as property
 * Lighter than PropertyAble
 *
 */
trait PropertyAccess {

    use UnionType;

    /** {@inheritdoc} */
    public function &__get($name) {
        $getter = sprintf("get%s", Tools::toCamelCase($name));
        $value = null;
        if (method_exists($this, $getter)) {
            $props = $this->parseTypes($getter);
            if (count($props) === 0) $value = call_user_func_array([$this, $getter], []);
        }
        return $value;
    }

    /** {@inheritdoc} */
    public function __isset($name) {
        $getter = sprintf("get%s", Tools::toCamelCase($name));
        if (method_exists($this, $getter)) {
            $props = $this->parseTypes($getter);
            if (count($props) === 0) return true;
        }
        return false;
    }

    /** {@inheritdoc} */
    public function __set($name, $value) {
        $setter = sprintf("set%s", Tools::toCamelCase($name));
        if (method_exists($this, $setter)) {
            //resolve type hint (type conversion even in strict mode using __set ...)
            $props = $this->parseTypes($setter);
            $acceptedTypes = $props[0] ?? [];
            // count 1 as a normal setter use 1 value and not 2 or 0
            if (count($props) === 1) {
                if (!empty($acceptedTypes)) {
                    $valueType = is_object($value) ? get_class($value) : get_debug_type($value);
                    foreach ($acceptedTypes as $type) {

                        if (
                                $type == $valueType or
                                ((class_exists($type) or interface_exists($type)) and $valueType instanceof $type)
                        ) {
                            call_user_func_array([$this, $setter], [$value]);
                            return;
                        }
                    }
                    throw new TypeError(sprintf(
                                            'Invalid value for property %s::$%s: "%s" required, "%s" given.',
                                            get_class($this),
                                            $name,
                                            implode('|', $acceptedTypes),
                                            $valueType
                    ));
                }

                //no hint there, accepts all types (method has to do the type detection)
                call_user_func_array([$this, $setter], [$value]);
            }
        }
    }

    /** {@inheritdoc} */
    public function __unset($name) {
        $setter = sprintf("set%s", Tools::toCamelCase($name));
        if (method_exists($this, $setter)) {
            $args = $this->parseTypes($setter);
            if (
                    count($args) === 1 and
                    (count($args[0]) === 0 or in_array('null', $args[0]) ) //accepts null or all types
            ) call_user_func_array([$this, $setter], [null]);
        }
    }

}
