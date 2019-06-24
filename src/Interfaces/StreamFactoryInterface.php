<?php

namespace NGSOFT\Tools\Interfaces;

interface StreamFactoryInterface extends \Psr\Http\Message\StreamFactoryInterface {

    /**
     * A convenient getter to access the resource directly
     * @return resource
     */
    public function getResource();
}
