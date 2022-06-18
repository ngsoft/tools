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
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->definitions) || array_key_exists($id, $this->providers);
    }

    protected function isResolved(string $id): bool
    {
        $this->handleServiceProvidersResolution($id);

        if ($this->has($id)) {
            return ($this->definitions[$id] ?? null) instanceof Closure === false;
        }
        throw new NotFoundException($this, $id);
    }

}
