<?php

namespace NGSOFT\Tools\Interfaces;

interface ArrayKeyResolver {

    /**
     * Resolve given key name using the array and returns the list of keys to use
     * @param string $key
     * @param array $array
     * @return array|null
     */
    public function resolve(string $key, array $array): ?array;
}
