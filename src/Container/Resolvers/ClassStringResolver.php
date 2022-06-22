<?php

declare(strict_types=1);

namespace NGSOFT\Container\Resolvers;

use NGSOFT\Container\{
    ContainerInterface, ContainerResolver
};
use Psr\Container\ContainerExceptionInterface;

/**
 * Resolves entries with class name as value
 *
 * @phan-file-suppress PhanUnusedPublicMethodParameter
 */
class ClassStringResolver implements ContainerResolver
{

    protected ParameterResolver $resolver;

    public function getDefaultPriority(): int
    {
        return ContainerInterface::PRIORITY_HIGH + 1;
    }

    public function __invoke(ContainerInterface $container, string $id, mixed $value): mixed
    {

        if (is_string($value) && class_exists($value)) {
            $resolver = $this->resolver;
            try {
                return $resolver($container, $value, null);
            } catch (ContainerExceptionInterface) {

            }
        }


        return $value;
    }

    public function __construct()
    {
        $this->resolver = new ParameterResolver();
    }

}
