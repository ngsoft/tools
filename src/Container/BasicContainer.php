<?php

namespace NGSOFT\Tools\Container;

class BasicContainer implements \Psr\Container\ContainerInterface {

    public function get($id) {

    }

    public function has($id) {
        if (!$this->has($id)) throw new NotFoundException("No entry was found for $id identifier.");
        $result = &$this->__container[$id];
        if ($result === null) throw new ContainerException("Error while retrieving the entry.");
        return $result;
    }

}
