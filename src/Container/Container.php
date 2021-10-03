<?php

declare(strict_types=1);

namespace NGSOFT\ORM;

use NGSOFT\ORM\Exceptions\NotFoundException,
    Psr\Container\ContainerInterface;

/**
 * Basic Container with autowiring
 */
final class Container implements ContainerInterface {

    /** @var array<string,mixed> */
    private $storage = [];

    /**
     * @param array<string,mixed> $definitions
     */
    public function __construct(array $definitions = []) {
        $this->storage = $definitions;
    }

    /**
     * Add an Entry
     * @param string $id
     * @param mixed $value
     * @return static
     */
    public function set(string $id, $value): self {
        // cannot overwrite data
        if (!isset($this->storage[$id])) {
            $this->storage[$id] = $value;
        }
        return $this;
    }

    /** {@inheritdoc} */
    public function get(string $id) {
        if (!$this->has($id)) {
            throw new NotFoundException($id, $this);
        }

        return $this->storage[$id];
    }

    /** {@inheritdoc} */
    public function has(string $id) {
        return isset($this->storage[$id]) or class_exists($id);
    }

    /** {@inheritdoc} */
    public function __debugInfo() {
        // do not bloat var_dumps
        return [];
    }

}
