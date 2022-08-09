<?php

declare(strict_types=1);

namespace NGSOFT\Container\Resolvers;

class InjectProperties extends ContainerResolver
{

    private function getClassParents(object $instance): \Traversable
    {
        try {

            $reflector = null;

            while (($reflector = is_null($reflector) ? new \ReflectionClass($instance) : $reflector->getParentClass() ) !== false) {
                yield $reflector;
            }
        } catch (ReflectionException) {

        }
    }

    public function resolve(mixed $value): mixed
    {

        if (is_object($value)) {

            $properties = [];

            /** @var ReflectionClass $reflClass */
            foreach ($this->getClassParents($value) as $reflClass) {

            }



            /** @var ReflectionProperty $reflProp */
            foreach ((new \ReflectionClass($value))->getProperties() as $reflProp) {

            }
        }

        return $value;
    }

    public function getDefaultPriority(): int
    {
        return 1024;
    }

}
