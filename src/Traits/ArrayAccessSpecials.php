<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Traits;

use ArrayAccess,
    BadFunctionCallException,
    BadMethodCallException,
    Traversable;

/**
 *
 */
trait ArrayAccessSpecials {

    /** @var array */
    protected $storage = [];

    ////////////////////////////   Utils   ////////////////////////////

    /**
     * array_replace_recursive that appends numeric keys instead of replacing them
     * @param array $orig
     * @param array $array
     * @return array
     */
    protected function merge(array $orig, array $array): array {
        $result = $orig;
        foreach ($array as $key => $value) {
            if (is_int($key)) $result[] = $value;
            elseif (
                    is_array($value)
                    and isset($result[$key])
                    and is_array($result[$key])
            ) {
                $result[$key] = $this->merge($result[$key], $value);
            } else $result[$key] = $value;
        }
        return $result;
    }

    /**
     * Convert iterable to array
     * @param iterable $obj
     * @return array
     */
    protected function iterableToArray(iterable $obj): array {
        $result = [];
        foreach ($obj as $key => $value) {
            if (
                    is_iterable($value)
                    and ($value instanceof self or is_array($value))
            ) {
                $result[$key] = $this->iterableToArray($value);
            } else $result[$key] = $value;
        }
        return $result;
    }

    /**
     * Checks if trait is used in a traversable class
     * @throws BadMethodCallException
     */
    protected function assertTraversable() {
        if (!($this instanceof Traversable)) {
            throw new BadMethodCallException(sprintf('%s is not an instance of Traversable.', get_class($this)));
        }
    }

    /**
     * Checks if trait is used in a ArrayAccess class
     * @throws BadMethodCallException
     */
    protected function assertArrayAccess() {
        if (!($this instanceof ArrayAccess)) {
            throw new BadMethodCallException(sprintf('%s is not an instance of ArrayAccess.', get_class($this)));
        }
    }

    /**
     * Checks if value is boolean (callables return values)
     * @param mixed $value
     * @throws BadFunctionCallException
     */
    protected function assertIsBool($value) {
        if (!is_bool($value)) {
            throw new BadFunctionCallException(sprintf('Invalid return value: boolean requested, %s given.', gettype($value)));
        }
    }

    ////////////////////////////   JS Like Methods   ////////////////////////////

    /**
     * Concat multiples iterables on the internal storage
     * @param iterable ...$objects
     * @return static
     */
    public function concat(iterable ...$objects): self {
        foreach ($objects as $obj) {
            $array = $this->iterableToArray($obj);
            $this->storage = $this->merge($this->storage, $array);
        }
        return $this;
    }

    /**
     * Applies the callback to the elements of the storage and returns a copy
     * @param callable $callback a callback
     * @return static
     * @suppress PhanUndeclaredMethod, PhanTypeSuspiciousNonTraversableForeach, PhanTypeArraySuspicious
     */
    public function map(callable $callback): self {
        $this->assertTraversable();
        $this->assertArrayAccess();
        $result = new static();
        foreach ($this as $key => $value) {
            $result[$key] = $callback($value, $key);
        }
        return $result;
    }

    /**
     * Tests if all the elements from the storage pass the test implemented by the callable
     * @param callable $callback
     * @return boolean
     * @suppress PhanTypeSuspiciousNonTraversableForeach
     */
    public function every(callable $callback): bool {
        $this->assertTraversable();
        foreach ($this as $key => $value) {
            $retval = $callback($value, $key);
            $this->assertIsBool($retval);
            if (false === $retval) return false;
        }
        return true;
    }

    /**
     * Tests if at least one of the elements from the storage pass the test implemented by the callable
     * @param callable $callback
     * @return boolean
     * @suppress PhanTypeSuspiciousNonTraversableForeach
     */
    public function some(callable $callback): bool {
        $this->assertTraversable();
        foreach ($this as $key => $value) {
            $retval = $callback($value, $key);
            $this->assertIsBool($retval);
            if (true === $retval) return true;
        }
        return false;
    }

    /**
     * Returns a copy with all the elements that passes the test
     * @param callable $callback
     * @return static
     * @suppress PhanUndeclaredMethod, PhanTypeSuspiciousNonTraversableForeach, PhanTypeArraySuspicious
     */
    public function filter(callable $callback): self {
        $this->assertTraversable();
        $this->assertArrayAccess();
        $result = new static();
        foreach ($this as $key => $value) {
            $retval = $callback($value, $key);
            $this->assertIsBool($retval);
            if ($retval === true) $result[$key] = $value;
        }
        return $result;
    }

    /**
     * Runs the given callable for each of the elements
     * @param callable $callback
     * @return static
     * @suppress PhanTypeSuspiciousNonTraversableForeach
     */
    public function forEach(callable $callback): self {
        $this->assertTraversable();
        foreach ($this as $key => $value) $callback($value, $key);
        return $this;
    }

}
