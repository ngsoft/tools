<?php

namespace NGSOFT\Tools\Objects\Resolvers;

class NormalResolver implements \NGSOFT\Tools\Interfaces\ArrayKeyResolver {

    /** {@inheritdoc} */
    public function resolve(string $key, array $array) {
        if (array_key_exists($key, $array)) {
            return [$key];
        }
        return null;
    }

}
