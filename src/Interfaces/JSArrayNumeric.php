<?php

namespace NGSOFT\Tools\Interfaces;

interface JSArrayNumeric extends JSArrayInterface {

    const ARRAY_IS_NUMERIC = true;

    /**
     * The copyWithin() method shallow copies part of an array to another location in the same array and returns it without modifying its length.
     * @param int $target Zero-based index at which to copy the sequence to. If negative, target will be counted from the end.
     * @param int $start Zero-based index at which to start copying elements from. If negative, start will be counted from the end.
     * @param ?int $end Zero-based index at which to end copying elements from. copyWithin copies up to but not including end. If negative, end will be counted from the end.
     * @return static The modified array.
     */
    public function copyWithin(int $target, int $start = 0, int $end = null): self;

    /**
     * The fill() method fills (modifies) all the elements of an array from a start index (default zero) to an end index (default array length) with a static value. It returns the modified array.
     * @param mixed $value Value to fill an array.
     * @param int $start Start index, defaults to 0
     * @param ?int $end Defaults to full length
     * @return static
     */
    public function fill($value, int $start = 0, int $end = null): self;

    /**
     * The flat() method creates a new array with all sub-array elements concatenated into it recursively up to the specified depth.
     * @param int $depth
     * @return static
     */
    public function flat(int $depth = 1): self;

    /**
     * The flatMap() method first maps each element using a mapping function, then flattens the result into a new array.
     * @param callable $callback
     * @return static
     */
    public function flatMap(callable $callback): self;

    /**
     * The slice() method returns a shallow copy of a portion of an array into a new array object selected from begin to end (end not included). The original array will not be modified.
     * @param int $start
     * @param ?int $end
     * @return \self
     */
    public function slice(int $start = 0, int $end = null): self;

    /**
     * The splice() method changes the contents of an array by removing or replacing existing elements and/or adding new elements in place.
     * @param int $start
     * @param mixed ...$args if first arg is int it will be used as $length
     * @return static
     */
    public function splice(int $start, ...$args): self;

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
}
