<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Objects\Resolvers;

class CaseInsensitiveResolver implements \NGSOFT\Tools\Interfaces\ArrayKeyResolver {

    public function resolve(string $key, array $array): ?array {
        $norm = strtolower($key);
        foreach (array_keys($array) as $arrayKey) {
            $arrayKeyNorm = strtolower($arrayKey);
            if ($norm === $arrayKeyNorm) {
                return [$arrayKey];
            }
        }
        return null;
    }

}
