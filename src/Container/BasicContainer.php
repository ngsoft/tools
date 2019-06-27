<?php

namespace NGSOFT\Tools\Container;

use NGSOFT\Tools\Exceptions\ContainerException;
use NGSOFT\Tools\Exceptions\NotFoundException;
use Psr\Container\ContainerInterface;

class BasicContainer implements ContainerInterface {

    protected $storage = [];
    protected $factory = [];

    /** {@inheritdoc} */
    public function get($id) {
        assert(is_string($id));
        if (!$this->has($id)) throw new NotFoundException("No entry was found for $id identifier.");
        $result = $this->storage[$id];
        if ($result === null) throw new ContainerException("Error while retrieving the entry.");
        return $result;
    }

    /** {@inheritdoc} */
    public function has($id) {
        assert(is_string($id));
        return array_key_exists($id, array_merge($this->storage, $this->factory));
    }

}
