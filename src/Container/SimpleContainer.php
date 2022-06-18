<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure;

/**
 * Container with only basic functionality
 */
final class SimpleContainer extends ContainerAbstract
{

    /** {@inheritdoc} */
    public function get(string $id): mixed
    {
        if ( ! $this->isResolved($id)) {
            $resolved = call_user_func($this->definitions[$id], $this);
            $this->definitions[$id] = $this->handle($id, $resolved);
        }
        return $this->definitions[$id];
    }

    /** {@inheritdoc} */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->definitions) || array_key_exists($id, $this->providers);
    }

    protected function isResolved(string $id): bool
    {
        $this->handleServiceProvidersResolution($id);

        if ( ! $this->has($id)) {
            throw new NotFoundException($this, $id);
        }

        return $this->definitions[$id] instanceof Closure === false;
    }

}
