<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

use Psr\Container\ContainerInterface;

trait ContainerAware {

    /**
     * PHP-DI autowiring support
     * @Inject
     * @var ContainerInterface
     */
    private $container;

    /**
     * Access the Container
     *
     * @return ContainerInterface|null
     */
    protected function getContainer(): ?ContainerInterface {
        return $this->container;
    }

    /**
     * Register a Container
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function setContainer(ContainerInterface $container): void {
        $this->container = $container;
    }

}
