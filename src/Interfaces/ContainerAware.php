<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Interfaces;

use Psr\Container\ContainerInterface;

interface ContainerAware {

    /**
     * Set the Container
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container);
}
