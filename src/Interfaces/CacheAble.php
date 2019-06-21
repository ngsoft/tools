<?php

namespace NGSOFT\Tools\Interfaces;

use Illuminate\Support\Contracts\ArrayableInterface;

/**
 * If an object has to be cached it must implements that interface
 * @method array toArray() Cache will call that method whenever the data is cached
 */
interface CacheAble extends ArrayableInterface {

    /**
     * Cache will call that method whenever the data is retrieved
     * @param array $data data to inject into the class
     * @return self instance of the CacheAble object
     */
    public static function __set_state(array $data): self;
}
