<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Objects\Resolvers;

use NGSOFT\Tools\Interfaces\ArrayKeyResolver;

class CaseSensitiveResolver implements ArrayKeyResolver {

    /** {@inheritdoc} */
    public function resolve(string $key, array $array): ?array {
        if (array_key_exists($key, $array)) {
            return [$key];
        }
        return null;
    }

}
