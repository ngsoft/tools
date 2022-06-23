<?php

declare(strict_types=1);

namespace NGSOFT\Profiler\Models;

/**
 * @phan-file-suppress PhanUndeclaredMethod, PhanUndeclaredStaticMethod
 */
trait ReturnTypeParser
{

    public function getReturnTypes(): array
    {

        $types = 'mixed';

        if ($this->hasReturnType()) {
            $types = $this->getReturnType();
        } elseif ($this->hasTentativeReturnType()) {
            $types = $this->getTentativeReturnType();
        }

        $str = (string) $types;
        $result = [];

        if ($str[0] === '?') {
            $str = mb_substr($str, 1);
            $result['null'] = 'null';
        }

        foreach (preg_split('#[\&\|]+#', $str) as $type) {
            $result[$type] = self::isBuiltinType($type) ? strtolower($type) : $type;
        }

        return array_values($result);
    }

}
