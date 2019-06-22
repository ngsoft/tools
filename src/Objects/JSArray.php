<?php

namespace NGSOFT\Tools\Objects;

use ArrayObject;
use NGSOFT\Tools\Interfaces\JSArrayInterface;
use NGSOFT\Tools\Traits\JSArrayMethods;

/**
 * A library that reproduces the Javascript Array Object for PHP
 * On that class keys can be non numeric
 * @link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array
 */
class JSArray extends ArrayObject implements JSArrayInterface {

    use JSArrayMethods;

    /** @param iterable $input */
    public function __construct(iterable $input = []) {
        parent::__construct($input);
    }

    /** {@inheritdoc} */
    public static function From($value, callable $mapFn = null): JSArrayInterface {
        assert(is_iterable($value));
        $array = (array) $value;
        if (is_callable($mapFn)) $array = array_map($mapFn, $array);
        return new static($array);
    }

    /** {@inheritdoc} */
    public static function isArray($value): bool {
        return is_array($value);
    }

    /** {@inheritdoc} */
    public static function isIterable($value): bool {
        return is_iterable($value);
    }

    /** {@inheritdoc} */
    public static function of(...$values): JSArrayInterface {
        return new static($values);
    }

}
