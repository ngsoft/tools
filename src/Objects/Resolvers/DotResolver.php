<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Objects\Resolvers;

/**
 * Resolves Dot Properties
 */
class DotResolver extends ChainResolver {

    public function __construct() {
        parent::__construct(
                new CaseSensitiveResolver(),
                new CaseInsensitiveResolver(),
                new UnderScoreResolver()
        );
    }

    /** {@inheritdoc} */
    public function resolve(string $key, array $array): ?array {

        if (mb_strpos($key, '.') === false) return null;

        $params = explode('.', $key);
        $result = [];
        foreach ($params as $param) {
            if (
                    is_array($array)
                    and array_key_exists($param, $result)
            ) {
                $result[] = $param;
                $array = $array[$param];
            } else return null;
        }
        return $result;
    }

}
