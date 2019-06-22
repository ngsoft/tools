<?php

namespace NGSOFT\Tools\Interfaces;

interface JSArrayNumeric extends JSArrayInterface {

    /**
     * The copyWithin() method shallow copies part of an array to another location in the same array and returns it without modifying its length.
     * @param int $target Zero-based index at which to copy the sequence to. If negative, target will be counted from the end.
     * @param int $start Zero-based index at which to start copying elements from. If negative, start will be counted from the end.
     * @param ?int $end Zero-based index at which to end copying elements from. copyWithin copies up to but not including end. If negative, end will be counted from the end.
     * @return static The modified array.
     */
    public function copyWithin(int $target, int $start = 0, int $end = null): JSArrayNumeric;

    /**
     * The fill() method fills (modifies) all the elements of an array from a start index (default zero) to an end index (default array length) with a static value. It returns the modified array.
     * @param mixed $value Value to fill an array.
     * @param int $start Start index, defaults to 0
     * @param ?int $end Defaults to full length
     * @return JSArrayNumeric
     */
    public function fill($value, int $start = 0, int $end = null): JSArrayNumeric;
}
