<?php

declare(strict_types=1);

namespace NGSOFT\Container\Resolvers;

use NGSOFT\Container\{
    ContainerInterface, ContainerResolver
};
use Psr\Container\ContainerExceptionInterface;

class ClassStringResolver implements ContainerResolver
{

    public function __invoke(ContainerInterface $container, string $id, mixed $value): mixed
    {


        if (is_string($value) && class_exists($value)) {


            try {
                return $container->get($value);
            } catch (ContainerExceptionInterface) {

            }
        }


        return $value;
    }

}
