<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Objects\Resolvers;

use NGSOFT\Tools\Interfaces\ArrayKeyResolver;

class UnderScoreResolver implements ArrayKeyResolver {

    public function resolve(string $key, array $array): ?array {

        if (mb_strpos($key, '_') === false) return null;

        $norm = strtolower($key);

        foreach (array_keys($array) as $arrayKey) {
            $arrayKeyNorm = strtolower($arrayKey);
            $arrayKeyNorm = str_replace(['-'], '_', $arrayKeyNorm);
            if ($norm === $arrayKeyNorm) {
                return [$arrayKey];
            }
        }
        return null;
    }

}
