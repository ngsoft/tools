<?php

namespace NGSOFT\Tools\Objects;

class Collection extends SimpleObject {

    public function export(): array {
        return $this->storage;
    }

    public function import(array $attributes) {
        $this->storage = [];
        foreach ($attributes as $k => $v) {
            $this->offsetSet($k, $v);
        }
    }

    public function addItem($item) {
        $this->offsetSet(null, $item);
        return $this;
    }

    public function removeItem($item) {
        $id = $this->findItem($item);
        if (is_int($id)) {
            $this->offsetUnset($id);
        }
        return $this;
    }

    public function hasItem($item): bool {
        return $this->findItem($item) !== false;
    }

    public function findItem($item) {
        return array_search($item, $this->storage, true);
    }

    public function offsetSet($k, $v) {
        if (!is_int($k) and ! is_null($k)) {
            return;
        }
        if (is_array($v)) {
            if (is_int(key($v))) {
                return parent::offsetSet($k, new Collection($v));
            }
            return parent::offsetSet($k, new Params($v));
        }
        parent::offsetSet($k, $v);
    }

}
