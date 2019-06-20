<?php

namespace NGSOFT\Tools\Interfaces;

interface CacheAble {

    /**
     * Cache will call that method whenever the data is retrieved
     * @param array $data data to inject into the class
     * @return self instance of the CacheAble object
     */
    public static function __set_state(array $data): self;

    /**
     * Cache will call that method whenever the data is cached
     * @return array data to put into cache
     */
    public function __get_state(): array;
}
