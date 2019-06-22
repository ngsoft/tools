<?php

namespace NGSOFT\Tools\Interfaces;

/**
 * If an object has to be cached it must implements that interface
 * @method array toArray()
 */
interface CacheAble {

    /**
     * Cache will call that method whenever the data is retrieved
     * @param array $data data to inject into the class
     * @return self instance of the CacheAble object
     */
    public static function createFromArray(array $data): self;

    /**
     * Cache will call that method whenever the data is cached
     * @return array Key values pair to import
     */
    public function toArray(): array;
}
