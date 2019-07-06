<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Interfaces;

/**
 * If an object has to be cached it must implements that interface
 */
interface CacheAble {

    /**
     * Cache will call that method whenever the data is retrieved
     * @param array $data data to inject into the class
     * @return static instance of the CacheAble object
     */
    public static function __set_state(array $data);

    /**
     * Cache will call that method whenever the data is cached
     * @return array Key values pair to import
     */
    public function toArray(): array;
}
