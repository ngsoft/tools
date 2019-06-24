<?php

namespace NGSOFT\Tools\Interfaces;

interface StreamInterface extends \Psr\Http\Message\StreamInterface {

    /**
     * A convenient getter to access the resource directly
     * @return resource
     */
    public function getResource();
}
