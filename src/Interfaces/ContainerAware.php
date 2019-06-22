<?php

namespace NGSOFT\Tools\Interfaces;

use Psr\Container\ContainerInterface;

interface ContainerAware {

    /**
     * Set the Container
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container);
}
