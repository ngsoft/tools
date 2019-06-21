<?php

namespace NGSOFT\Tools\Objects;

use Illuminate\Support\Collection as LaravelCollection;
use function NGSOFT\Tools\array_every;

class Collection extends LaravelCollection {

    /**
     * Test if all elements of the collection pass the given test
     * @param callable $callback
     * @return bool
     */
    public function every(callable $callback): bool {
        return array_every($callback, $this->items);
    }

    /**
     * Test if at least one element of the collection pass the given test
     * @param callable $callback
     * @return bool
     */
    public function some(callable $callback): bool {
        return !$this->every($callback);
    }

}
