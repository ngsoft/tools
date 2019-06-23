<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Interfaces;

use ArrayAccess;
use BadMethodCallException;
use Countable;
use JsonSerializable;
use Serializable;
use Traversable;

interface JSArrayInterface extends Traversable, ArrayAccess, Serializable, Countable, JsonSerializable {

    /**
     * Export to an array
     * @return array
     */
    public function toArray(): array;

    ////////////////////////////   JS Functions Numeric   ////////////////////////////

    /**
     * The fill() method fills (modifies) all the elements of an array from a start index (default zero) to an end index (default array length) with a static value. It returns the modified array.
     * @param mixed $value Value to fill an array.
     * @param int $num Number of items to replace
     * @param int $start Start index, defaults to 0
     * @return static
     * @throws BadMethodCallException if array has no numerical indexes
     */
    public function fill($value, int $num, int $start = 0);

    /**
     * The flat() method creates a new array with all sub-array elements concatenated into it recursively up to the specified depth.
     * @param int $depth
     * @return static
     */
    public function flat(int $depth = 1);

    /**
     * The flatMap() method first maps each element using a mapping function, then flattens the result into a new array.
     * @param callable $callback
     * @return static
     * @throws BadMethodCallException if array has no numerical indexes
     */
    public function flatMap(callable $callback);

    /**
     * The slice() method returns a shallow copy of a portion of an array into a new array object selected from begin with the required length.
     * @param int $start
     * @param ?int $length
     * @return static
     * @throws BadMethodCallException if array has no numerical indexes
     */
    public function slice(int $start = 0, int $length = null);

    /**
     * The splice() method changes the contents of an array by removing or replacing existing elements and/or adding new elements in place.
     * @param int $start
     * @param mixed ...$args if first arg is int it will be used as $length
     * @return static
     * @throws BadMethodCallException if array has no numerical indexes
     */
    public function splice(int $start, ...$args);

    /**
     * The toString() method returns a string representing the specified array and its elements.
     * @return string
     */
    public function toString(): string;

    /**
     * The push() method adds one or more elements to the end of an array and returns the new length of the array.
     * @param mixed ...$values
     * @return int
     */
    public function push(...$values): int;

    /**
     * The unshift() method adds one or more elements to the beginning of an array and returns the new length of the array.
     * @param mixed ...$values
     * @return int
     */
    public function unshift(...$values): int;


    ////////////////////////////   JS Function Both   ////////////////////////////

    /**
     * Returns the current length from an Array
     * @return int
     */
    public function length(): int;

    /**
     * The Array.from() method creates a new, shallow-copied Array instance from an array-like or iterable object.
     * @param string|iterable $value if string, string will be exploded as an array
     * @param ?callable $mapFn Map function to call on every element of the array.
     * @return static
     */
    public static function From($value, callable $mapFn = null);

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
     * @return static
     */
    public static function of(...$values);

    /**
     * The concat() method is used to merge two or more arrays. This method does not change the existing arrays, but instead returns a new array.
     * @param iterable ...$values
     * @return static A new object with the values added
     */
    public function concat(iterable ...$values);

    /**
     * The entries() method returns a new Array Iterator object that contains the key/value pairs for each index in the array.
     * @return iterable
     */
    public function entries(): iterable;

    /**
     * The every() method tests whether all elements in the array pass the test implemented by the provided function. It returns a Boolean value.
     * @param callable $callback A function to test for each element, taking 2 arguments: element and index
     * @return bool
     */
    public function every(callable $callback): bool;

    /**
     * The filter() method creates a new array with all elements that pass the test implemented by the provided function.
     * @param callable $callback A function to test for each element, taking 2 arguments: element and index
     * @return static
     */
    public function filter(callable $callback);

    /**
     * The find() method returns the value of the first element in the array that satisfies the provided testing function. Otherwise null is returned.
     * @param callable $callback A function to test for each element, taking 2 arguments: element and index
     * @return mixed
     */
    public function find(callable $callback);

    /**
     * The findIndex() method returns the index of the first element in the array that satisfies the provided testing function. Otherwise, it returns false, indicating that no element passed the test.
     * @param callable $callback A function to test for each element, taking 2 arguments: element and index
     * @return int|string|false
     */
    public function findIndex(callable $callback);

    /**
     * The forEach() method executes a provided function once for each array element.
     * @param callable $callback takes 2 arguments: element and index
     * @return void
     */
    public function forEach(callable $callback): void;

    /**
     * The includes() method determines whether an array includes a certain value among its entries, returning true or false as appropriate.
     * @param mixed $value Description
     * @return bool
     */
    public function includes($value): bool;

    /**
     * The indexOf() method returns the first index at which a given element can be found in the array, or false if it is not present.
     * @param mixed $value
     * @return string|int|false
     */
    public function indexOf($value);

    /**
     * The join() method creates and returns a new string by concatenating all of the elements separated by commas or a specified separator string. If the array has only one item, then that item will be returned without using the separator.
     * @param string $glue
     * @return string
     */
    public function join(string $glue): string;

    /**
     * The keys() method returns a new Iterator object that contains the keys for each index in the array.
     * @return iterable
     */
    public function keys(): iterable;

    /**
     * The lastIndexOf() method returns the last index at which a given element can be found in the array, or false if it is not present.
     * @param mixed $value
     * @return string|int|false Description
     */
    public function lastIndexOf($value);

    /**
     * The map() method creates a new array with the results of calling a provided function on every element in the calling array.
     * @param callable $callback takes 2 arguments: element and index
     * @return static
     */
    public function map(callable $callback);

    /**
     * The pop() method removes the last element from an array and returns that element. This method changes the length of the array.
     * @return mixed The removed element from the array; null if the array is empty.
     */
    public function pop();

    /**
     * The reduce() method executes a reducer function on each element of the array, resulting in a single output value.
     * @param callable $callback takes 2 arguments: carry(Holds the return value of the previous iteration; in the case of the first iteration it instead holds the value of initial) and item(Holds the value of the current iteration)
     * @param mixed $initial
     * @return mixed
     */
    public function reduce(callable $callback, $initial = null);

    /**
     * The same as reduce() but in reverse
     * @param callable $callback
     * @param mixed $initial
     * @return mixed
     */
    public function reduceRight(callable $callback, $initial = null);

    /**
     * The reverse() method reverses an array in place. The first array element becomes the last, and the last array element becomes the first.
     * @return static
     */
    public function reverse();

    /**
     * The shift() method removes the first element from an array and returns that removed element. This method changes the length of the array.
     * @return mixed
     */
    public function shift();

    /**
     * The some() method tests whether at least one element in the array passes the test implemented by the provided function. It returns a Boolean value.
     * @param callable $callback
     * @return bool
     */
    public function some(callable $callback): bool;

    /**
     * The sort() method sorts the elements of an array in place and returns the sorted array.
     * @param callable $callback
     * @return static
     */
    public function sort(callable $callback);

    /**
     * The values() method returns a new Array Iterator object that contains the values for each index in the array.
     * @return iterable
     */
    public function values(): iterable;
}
