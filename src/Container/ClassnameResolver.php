<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Invoker\{
    Invoker, ParameterResolver\AssociativeArrayResolver, ParameterResolver\Container\ParameterNameContainerResolver, ParameterResolver\Container\TypeHintContainerResolver,
    ParameterResolver\DefaultValueResolver, ParameterResolver\NumericArrayResolver, ParameterResolver\ResolverChain
};
use Psr\Container\ContainerInterface;

class ClassnameResolver {

    /** @var ContainerInterface */
    private $container;

    /** @var Invoker */
    private $invoker;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $chain = new ResolverChain([
            new TypeHintContainerResolver($container),
            new ParameterNameContainerResolver($container),
            new NumericArrayResolver(),
            new AssociativeArrayResolver(),
            new DefaultValueResolver(),
        ]);

        $invoker = $this->invoker = new Invoker($chain, $container);
    }

    public function resolve(string $className) {

    }

}
