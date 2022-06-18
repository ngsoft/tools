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
            $this->definitions[$id] = $this->handle($resolved);
        }
        return $this->definitions[$id];
    }

    /** {@inheritdoc} */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->definitions) || array_key_exists($id, $this->providers);
    }

    private function isResolved(string $key): bool
    {
        $this->handleServiceProvidersResolution($id);

        if ( ! $this->has($key)) {
            throw new NotFoundException($this, $key);
        }

        return $this->definitions[$key] instanceof Closure === false;
    }

}
