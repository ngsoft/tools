<?php

declare(strict_types=1);

namespace NGSOFT\Container;

use Closure,
    NGSOFT\Exceptions\NotFoundException,
    Psr\Container\ContainerInterface;

/**
 * Container with only basic functionality
 */
final class SimpleContainer extends Container
{

    /** {@inheritdoc} */
    public function get(string $id): mixed
    {
        if (!$this->isResolved($id)) {


            $resolved = call_user_func($this->definitions[$id], $this);

            foreach ($this->handlers as $handler) {
                $resolved = $handler($this, $resolved);
            }
            $this->definitions[$id] = $resolved;
        }
        return $this->definitions[$id];
    }

    /** {@inheritdoc} */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->definitions);
    }

    private function isResolved(string $key): bool
    {
        if (!$this->has($key)) {
            throw new NotFoundException($this, $key);
        }

        return $this->definitions[$key] instanceof Closure === false;
    }

}
