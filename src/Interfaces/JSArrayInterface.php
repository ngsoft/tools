<?php

namespace NGSOFT\Tools\Interfaces;

use Traversable;

interface JSArrayInterface extends Traversable {

    /**
     * Used to determine if main class has numerical indexes
     * or both
     */
    const ARRAY_IS_NUMERIC = false;

    /**
     * Export to an array
     * @return array
     */
    public function toArray(): array;

    ////////////////////////////   JS Functions Numeric   ////////////////////////////

    /**
     * The copyWithin() method shallow copies part of an array to another location in the same array and returns it without modifying its length.
     * @param int $target Zero-based index at which to copy the sequence to. If negative, target will be counted from the end.
     * @param int $start Zero-based index at which to start copying elements from. If negative, start will be counted from the end.
     * @param ?int $end Zero-based index at which to end copying elements from. copyWithin copies up to but not including end. If negative, end will be counted from the end.
     * @return static The modified array.
     * @throws \BadMethodCallException if array has no numerical indexes
     */
    public function copyWithin(int $target, int $start = 0, int $end = null): self;

    /**
     * The fill() method fills (modifies) all the elements of an array from a start index (default zero) to an end index (default array length) with a static value. It returns the modified array.
     * @param mixed $value Value to fill an array.
     * @param int $start Start index, defaults to 0
     * @param ?int $end Defaults to full length
     * @return static
     * @throws \BadMethodCallException if array has no numerical indexes
     */
    public function fill($value, int $start = 0, int $end = null): self;

    /**
     * The flat() method creates a new array with all sub-array elements concatenated into it recursively up to the specified depth.
     * @param int $depth
     * @return static
     * @throws \BadMethodCallException if array has no numerical indexes
     */
    public function flat(int $depth = 1): self;

    /**
     * The flatMap() method first maps each element using a mapping function, then flattens the result into a new array.
     * @param callable $callback
     * @return static
     * @throws \BadMethodCallException if array has no numerical indexes
     */
    public function flatMap(callable $callback): self;

    /**
     * The slice() method returns a shallow copy of a portion of an array into a new array object selected from begin to end (end not included). The original array will not be modified.
     * @param int $start
     * @param ?int $end
     * @return JSArrayInterface
     * @throws \BadMethodCallException if array has no numerical indexes
     */
    public function slice(int $start = 0, int $end = null): JSArrayInterface;

    /**
     * The splice() method changes the contents of an array by removing or replacing existing elements and/or adding new elements in place.
     * @param int $start
     * @param mixed ...$args if first arg is int it will be used as $length
     * @return JSArrayInterface
     * @throws \BadMethodCallException if array has no numerical indexes
     */
    public function splice(int $start, ...$args): JSArrayInterface;

    /**
     * The toString() method returns a string representing the specified array and its elements.
     * @return string
     */
    public function toString(): string;

    /**
     * The push() method adds one or more elements to the end of an array and returns the new length of the array.
     * @param type $values
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
     * @return JSArrayInterface
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
     * @return Traversable
     */
    public function entries(): Traversable;

    /**
     * The every() method tests whether all elements in the array pass the test implemented by the provided function. It returns a Boolean value.
     * @param callable $callback A function to test for each element, taking 2 arguments: element and index
     * @return JSArrayInterface
     */
    public function every(callable $callback): JSArrayInterface;

    /**
     * The filter() method creates a new array with all elements that pass the test implemented by the provided function.
     * @param callable $callback A function to test for each element, taking 2 arguments: element and index
     * @return JSArrayInterface
     */
    public function filter(callable $callback): JSArrayInterface;

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
     * @return JSArrayInterface
     */
    public function map(callable $callback): JSArrayInterface;

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
    public function reduce(callable $callback, $initial);

    /**
     * The same as reduce() but in reverse
     * @param callable $callback
     * @return mixed
     */
    public function reduceRight(callable $callback);

    /**
     * The reverse() method reverses an array in place. The first array element becomes the last, and the last array element becomes the first.
     * @return static
     */
    public function reverse(): self;

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
    public function sort(callable $callback): self;

    /**
     * The values() method returns a new Array Iterator object that contains the values for each index in the array.
     * @return iterable
     */
    public function values(): iterable;
}
