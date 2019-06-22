<?php

namespace NGSOFT\Tools\Interfaces;

interface JSArrayInterface extends \Traversable {

    /**
     * The Array.from() method creates a new, shallow-copied Array instance from an array-like or iterable object.
     * @param string|iterable $value if string, string will be exploded as an array
     * @param ?callable $mapFn Map function to call on every element of the array.
     * @return static
     */
    public static function From($value, callable $mapFn = null): JSArrayInterface;

    /**
     * The Array.isArray() method determines whether the passed value is an array
     * @param mixed $value
     * @return bool
     */
    public static function isArray($value): bool;

    /**
     * The Array.isIterable() method determines whether the passed value is an array
     * @param mixed $value
     * @return bool
     */
    public static function isIterable($value): bool;

    /**
     * The Array.of() method creates a new Array instance from a variable number of arguments, regardless of number or type of the arguments.
     * @param mixed ...$values
     * @return JSArrayInterface
     */
    public static function of(...$values): JSArrayInterface;

    /**
     * The concat() method is used to merge two or more arrays. This method does not change the existing arrays, but instead returns a new array.
     * @param iterable ...$values
     * @return JSArrayInterface A new object with the values added
     */
    public function concat(iterable ...$values): JSArrayInterface;

    /**
     * The entries() method returns a new Array Iterator object that contains the key/value pairs for each index in the array.
     * @return \Traversable
     */
    public function entries(): \Traversable;

    /**
     * The every() method tests whether all elements in the array pass the test implemented by the provided function. It returns a Boolean value.
     * @param callable $callback A function to test for each element, taking 2 arguments: element and index
     * @return JSArrayInterface
     */
    public function every(callable $callback): JSArrayInterface;
}
