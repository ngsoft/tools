<?php

declare(strict_types=1);

namespace NGSOFT\Reflection;

use Attribute,
    Psr\Cache\CacheItemPoolInterface,
    ReflectionAttribute,
    ReflectionClass,
    ReflectionException;

class AttributeReader {

    private ?CacheItemPoolInterface $cachePool = null;

    private function isRepeatableAttribute(string $attributeName): bool {
        static $cache = [];

        if (!is_bool($cache[$attributeName])) {

            $result = false;

            try {

                $reflectionClass = new ReflectionClass($attributeName);
                foreach ($reflectionClass->getAttributes(Attribute::class, ReflectionAttribute::IS_INSTANCEOF) as $reflectionAttribute) {
                    $attribute = $reflectionAttribute->newInstance();
                    $result = ($attribute->flags & Attribute::IS_REPEATABLE) > 0;
                    break;
                }
            } catch (ReflectionException) {

            }

            $cache[$attributeName] = $result;
        }

        return $cache[$attributeName];
    }

    private function getAttributeInstances(ReflectionAttribute ...$attributes): array {

        $result = [];

        foreach ($attributes as $reflectionAttribute) {

            try {
                $attributeName = $reflectionAttribute->getName();
                $instance = $reflectionAttribute->newInstance();

                if ($this->isRepeatableAttribute($attributeName)) {

                }
            } catch (ReflectionException) {

            }
        }







        return $result;
    }

}
