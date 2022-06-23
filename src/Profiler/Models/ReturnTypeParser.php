<?php

declare(strict_types=1);

namespace NGSOFT\Profiler\Models;

use function mb_substr,
             NGSOFT\Tools\map;

/**
 * @phan-file-suppress PhanUndeclaredMethod, PhanUndeclaredStaticMethod
 */
abstract class ReturnTypeParser extends BaseModel
{

    /**
     * @return Type[]
     */
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
            $result[$type] = $type;
        }

        return map(fn($type) => Type::create($type), array_values($result));
    }

}
