<?php

namespace NGSOFT\Tools\Traits;

use Psr\Container\ContainerInterface;

trait Container {

    /** @var ContainerInterface */
    protected $container;

    /**
     * Get Item from Container
     * @param string $key
     * @return mixed
     */
    protected function get(string $key) {
        return isset($this->container) ? $this->container->get($key) : null;
    }

    /**
     * Checks if container has item
     * @param string $key
     * @return bool
     */
    protected function has(string $key): bool {
        return isset($this->container) ? $this->container->has($key) : false;
    }

    /**
     * Inject the Container
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container) {
        $this->container = $container;
    }

}
