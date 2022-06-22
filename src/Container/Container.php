<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure,
    NGSOFT\Container\Resolvers\ParameterResolver;

/**
 * Container that supports autowiring for dependency injection
 *
 * @phan-file-suppress PhanTypeMismatchArgumentSuperType
 */
class Container extends ContainerAbstract
{

    public function __construct(array $definitions = [])
    {
        parent::__construct($definitions);
        $this->addResolutionHandler(new ParameterResolver());
    }

}
