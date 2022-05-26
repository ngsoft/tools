<?php

declare(strict_types=1);

namespace NGSOFT\Attributes;

use Attribute,
    ReflectionClass,
    ReflectionException;

trait AttributeTrait {

    public string $name = '';
    public ?int $attributeTargetType = null;

    /**
     * @staticvar array $cache
     * @param string|object $className
     * @return Property[]
     */
    public static function getPropertyAttributes(string|object $className): array {

        static $cache = [];

        $key = is_string($className) ?? $className::class;
        if (isset($cache[$key])) return $cache[$key];

        try {

            $result = [];

            $reflClass = new ReflectionClass($className);

            do {
                /** @var \ReflectionProperty $prop */
                foreach ($reflClass->getProperties() as $prop) {
                    $name = $prop->getName();
                    if (isset($result[$name])) continue;
                    /** @var \ReflectionAttribute $attr */
                    foreach ($prop->getAttributes(static::class) as $attr) {
                        $instance = $attr->newInstance();
                        $instance->name = $name;
                        $instance->attributeTargetType = Attribute::TARGET_PROPERTY;

                        $result[$name] = $instance;
                    }
                }
            } while (($reflClass = $reflClass->getParentClass()) !== false);

            return $cache[$key] = $result;
        } catch (ReflectionException) {
            return [];
        }
    }

}
