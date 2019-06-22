<?php

namespace NGSOFT\Tools\Objects;

use ArrayObject;
use NGSOFT\Tools\Interfaces\JSArrayInterface;

/**
 * A library that reproduces the Javascript Array Object for PHP
 * On that class keys can be non numeric
 * @link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array
 */
class JSArray extends ArrayObject implements JSArrayInterface {

}
