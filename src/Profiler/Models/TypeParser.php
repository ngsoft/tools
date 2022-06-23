<?php

declare(strict_types=1);

namespace NGSOFT\Profiler\Models;

/**
 * @phan-file-suppress PhanUndeclaredMethod
 */
trait TypeParser
{

    /**
     * @link https://www.php.net/manual/en/language.types.declarations.php
     * @param string $type
     * @return bool
     */
    protected function isBuiltinType(string $type): bool
    {
        static $builtin = [
            'self', 'parent',
            'array', 'callable', 'bool', 'float', 'int', 'string', 'iterable', 'object', 'mixed',
            'void', 'never', 'static', 'null', 'false',
        ];

        return in_array(strtolower($type), $builtin);
    }

    public function isIntersectionType(): bool
    {
        return $this->getType() instanceof \ReflectionIntersectionType;
    }

    public function isUnionType(): bool
    {
        return $this->getType() instanceof \ReflectionUnionType;
    }

    public function isNamedType(): bool
    {
        return $this->getType() instanceof \ReflectionNamedType;
    }

    public function isMixedType(): bool
    {
        return in_array('mixed', $this->getTypes());
    }

    /**
     * @return string[]
     */
    public function getTypes(): array
    {

        $types = $this->getType();

        if (null === $types) {
            return ['mixed'];
        }

        $str = (string) $types;

        $result = [];

        if ($str[0] === '?') {
            $str = mb_substr($str, 1);
            $result['null'] = 'null';
        }

        foreach (preg_split('#[\&\|]+#', $str) as $type) {
            $result[$type] = $this->isBuiltinType($type) ? strtolower($type) : $type;
        }

        return array_values($result);
    }

}
