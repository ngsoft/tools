<?php

declare(strict_types=1);

namespace NGSOFT\Container\Resolvers;

use NGSOFT\{
    Container\Attribute\Inject, Container\Exceptions\ResolverException, Facades\Logger
};
use Psr\Container\ContainerExceptionInterface,
    ReflectionAttribute,
    ReflectionClass,
    ReflectionException,
    ReflectionIntersectionType,
    ReflectionProperty,
    Traversable;

/**
 * Scans for #[Inject] attribute on the loaded class properties
 */
class InjectProperties extends ContainerResolver
{

    private function getClassParents(object $instance): Traversable
    {
        try {

            $reflector = null;

            while (($reflector = is_null($reflector) ? new ReflectionClass($instance) : $reflector->getParentClass() ) !== false) {
                yield $reflector;
            }
        } catch (ReflectionException) {

        }
    }

    public function resolve(mixed $value): mixed
    {
        static $builtin = [
            'self', 'parent', 'static',
            'array', 'callable', 'bool', 'float', 'int', 'string', 'iterable', 'object', 'mixed',
            'void', 'never', 'null', 'false',
        ];

        if (is_object($value)) {

            $properties = [];

            /** @var ReflectionClass $reflClass */
            /** @var ReflectionProperty $reflProp */
            /** @var ReflectionAttribute $attribute */
            /** @var Inject $inject */
            foreach ($this->getClassParents($value) as $reflClass) {

                foreach ($reflClass->getProperties() as $reflProp) {
                    $name = $reflProp->getName();
                    if (isset($properties[$name])) {
                        continue;
                    }
                    $properties[$name] = true;
                    foreach ($reflProp->getAttributes(Inject::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                        $inject = $attribute->newInstance();

                        if (empty($inject->name)) {
                            if ($type = $reflProp->getType()) {
                                if ( ! ($type instanceof ReflectionIntersectionType)) {
                                    $inject->name = (string) $type;
                                }
                            }
                        }

                        if ( ! empty($inject->name)) {
                            foreach (explode('|', $inject->name) as $dep) {

                                $dep = preg_replace('#^\?#', '', $dep);

                                if ($dep === 'self') {
                                    $dep = $reflProp->getDeclaringClass()->getName();
                                } elseif (in_array($dep, $builtin)) {
                                    continue;
                                }

                                try {
                                    $entry = $this->container->get($dep);
                                    $reflProp->setAccessible(true);
                                    $reflProp->setValue($value, $entry);
                                    continue 2;
                                } catch (ContainerExceptionInterface) {

                                }
                            }
                        }
                        Logger::debug(sprintf(
                                        'Cannot use %s on object(%s)#%d::$%s',
                                        $inject, get_class($value), spl_object_id($value),
                                        $name
                        ));
                    }
                }
            }
        }

        return $value;
    }

    public function getDefaultPriority(): int
    {
        // loads first
        return 1024;
    }

}
