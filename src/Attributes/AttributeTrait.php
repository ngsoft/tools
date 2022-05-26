<?php

declare(strict_types=1);

namespace NGSOFT\Attributes;

trait AttributeTrait {

    public static function getPropertyAttributes(string $className): array {

        try {

            $result = [];

            $reflClass = new \ReflectionClass($className);

            do {
                /** @var \ReflectionProperty $prop */
                foreach ($reflClass->getProperties() as $prop) {
                    $name = $prop->getName();
                    if (isset($result[$name])) continue;
                    /** @var \ReflectionAttribute $attr */
                    foreach ($prop->getAttributes(static::class) as $attr) {
                        $result[$prop] = $attr->newInstance();
                    }
                }
            } while (($reflClass = $reflClass->getParentClass()) !== false);

            return $result;
        } catch (\ReflectionException) {
            return [];
        }
    }

}
