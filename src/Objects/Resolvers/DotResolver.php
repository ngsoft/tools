<?php

namespace NGSOFT\Tools\Objects\Resolvers;

/**
 * Resolves Dot Properties
 */
class DotResolver implements \NGSOFT\Tools\Interfaces\ArrayKeyResolver {

    /** {@inheritdoc} */
    public function resolve(string $key, array $array) {
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
