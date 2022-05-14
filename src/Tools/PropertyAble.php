<?php

declare(strict_types=1);

namespace NGSOFT\Tools;

#[\NGSOFT\Attributes\HasProperties]
class PropertyAble implements \ArrayAccess, \Countable, \IteratorAggregate {

    /** @var Property[] */
    private $properties = [];

    private function getAttributes() {

        $reflClass = new \ReflectionClass($this);
        $attrs = $reflClass->getAttributes(\NGSOFT\Attributes\HasProperties::class);
    }

    private function iterateEnumerableProperties(): \Traversable {
        foreach ($this->properties as $prop) {
            if ($prop->getEnumerable()) yield $prop->getName() => $prop;
        }
    }

    protected function defineProperty(
            string $name,
            mixed $value,
            bool $enumerable = true,
            bool $configurable = true,
            bool $writable = true
    ): static {
        /** @var Property $current */
        if ($current = $this->properties[$name] ?? null) {
            if (!$current->getConfigurable()) {
                throw new RuntimeException(sprintf('Cannot define property "%s": not configurable.', $name));
            }
        }

        $this->properties[$name] = new Property($name, $value, configurable: $configurable, enumerable: $enumerable, writable: $writable);
        return $this;
    }

    protected function removeProperty(string $name): static {
        /** @var Property $current */
        if ($current = $this->properties[$name]) {
            if (!$current->getConfigurable()) {
                throw new RuntimeException(sprintf('Cannot remove property "%s": not configurable.', $name));
            }

            unset($this->properties[$name]);
        }
        return $this;
    }

    private function getPropertyValue(string $name): mixed {

        return $this->properties[$name]?->getValue() ?? null;
    }

    private function setPropertyValue(string $name, mixed $value): void {
        if ($current = $this->properties[$name] ?? null) {
            $current->setValue($value);
            return;
        } else $this->defineProperty($name, $value, enumerable: true);
    }

    /** {@inheritdoc} */
    public function offsetExists(mixed $offset): bool {
        return isset($this->properties[$offset]);
    }

    /** {@inheritdoc} */
    public function offsetGet(mixed $offset): mixed {

        return $this->getPropertyValue($offset);
    }

    /** {@inheritdoc} */
    public function offsetSet(mixed $offset, mixed $value): void {
        $this->setPropertyValue($offset, $value);
    }

    /** {@inheritdoc} */
    public function offsetUnset(mixed $offset): void {
        $this->removeProperty($offset);
    }

    /** {@inheritdoc} */
    public function count(): int {

        return count($this->properties);
    }

    public function getIterator(): \Traversable {
        /** @var Property $prop */
        foreach ($this->iterateEnumerableProperties() as $prop) {
            yield $prop->getName() => $prop->getValue();
        }
    }

    public function __isset($name) {

        return $this->offsetExists($name);
    }

    public function __get($name) {

        return $this->offsetGet($name);
    }

    public function __set($name, $value) {
        $this->offsetSet($name, $value);
    }

    public function __unset($name) {
        $this->offsetUnset($name);
    }

    public function __clone() {
        $properties = array_keys($this->properties);
        foreach ($properties as $name) {
            $this->properties[$name] = clone $this->properties[$name];
        }
    }

}
