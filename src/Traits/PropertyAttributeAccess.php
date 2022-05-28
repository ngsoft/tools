<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

use NGSOFT\Attributes\Property,
    RuntimeException;

trait PropertyAttributeAccess {

    private function getPropertyInfo(string $name): Property {

        $attributes = Property::getPropertyAttributes($this);
        return $attributes[$name] ?? new Property();
    }

    public function __get(string $name): mixed {
        $info = $this->getPropertyInfo($name);
        if (!$info->readable) {
            throw new RuntimeException(sprintf('%s::$%s does not exists.', static::class, $name));
        }

        return $this->{$name};
    }

    public function __isset(string $name): bool {
        $info = $this->getPropertyInfo($name);
        return $info->readable ? $this->{$name} !== null : false;
    }

    public function __set(string $name, mixed $value): void {
        $info = $this->getPropertyInfo($name);
        if (!$info->writable) {
            throw new RuntimeException(sprintf('Cannot set %s::$%s.', static::class, $name));
        }
        $this->{$name} = $value;
    }

    public function __unset(string $name): void {
        $info = $this->getPropertyInfo($name);
        if (!$info->removable) {
            throw new RuntimeException(sprintf('Cannot unset %s::$%s.', static::class, $name));
        }

        unset($this->{$name});
    }

    public function __serialize(): array {

        $result = [];
        foreach (Property::getPropertyAttributes($this) as $name => $prop) {
            if ($prop->serializable) $result[$name] = $this->{$name};
        }

        return $result;
    }

    public function __unserialize(array $data): void {
        $result = [];
        foreach (Property::getPropertyAttributes($this) as $name => $prop) {
            if ($prop->serializable) $this->{$name} = $data[$name];
        }
    }

}
