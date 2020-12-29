<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Objects\Resolvers;

use NGSOFT\Tools\Interfaces\ArrayKeyResolver;

class ChainResolver implements ArrayKeyResolver {

    /** @var ArrayKeyResolver[] */
    protected $resolvers = [];

    /**
     * Chain the Resolvers
     * @param ArrayKeyResolver ...$resolvers
     */
    public function __construct(ArrayKeyResolver ... $resolvers) {
        $this->resolvers = $resolvers;
    }

    /**
     * Add a resolver to the stack
     * @param ArrayKeyResolver $resolver
     * @return static
     */
    public function addResolver(ArrayKeyResolver $resolver): self {
        $this->resolvers[] = $resolver;
        return $this;
    }

    /** {@inheritdoc} */
    public function resolve(string $key, array $array): ?array {

        foreach ($this->resolvers as $resolver) {
            $result = $resolver->resolve($key, $array);
            if (is_array($result)) return $result;
        }

        return null;
    }

}
