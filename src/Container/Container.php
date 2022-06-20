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

    /** {@inheritdoc} */
    protected function resolve(string $id, mixed $resolved): mixed
    {
        static $resolver;
        if ( ! $resolver) {
            $resolver = new ParameterResolver();
        }

        return parent::resolve($id, $resolver($this, $id, $resolved));
    }

    /** {@inheritdoc} */
    public function has(string $id): bool
    {
        $id = $this->handleAliasResolution($id);
        return array_key_exists($id, $this->definitions) || array_key_exists($id, $this->providers) || is_instanciable($id);
    }

    protected function isResolved(string $id): bool
    {
        if (array_key_exists($id, $this->definitions)) {
            return $this->definitions[$id] instanceof Closure === false;
        } elseif (class_exists($id)) {
            return false;
        }
        throw new NotFoundException($this, $id);
    }

}
