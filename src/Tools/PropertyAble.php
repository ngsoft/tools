<?php

declare(strict_types=1);

namespace NGSOFT\Tools;

use ArrayAccess,
    Countable,
    IteratorAggregate,
    NGSOFT\Attributes\HasProperties,
    ReflectionClass,
    RuntimeException,
    Traversable;

#[HasProperties]
class PropertyAble implements ArrayAccess, Countable, IteratorAggregate {

    /** @var Property[] */
    private $properties = [];

    private function getAttributes() {

        $reflClass = new ReflectionClass($this);
        $attrs = $reflClass->getAttributes(HasProperties::class);
    }

    private function createError(string $message, ...$replacements): \RuntimeException {
        $msg = sprintf($message, $replacements);
        return new \RuntimeException($msg);
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
        $value = null;
        if ($current = $this->properties[$name] ?? null) {
            $value = $current->getValue();
        }

        return $value;
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

    /** {@inheritdoc} */
    public function getIterator(): Traversable {
        /** @var Property $prop */
        foreach ($this->properties as $prop) {
            if ($prop->isEnumerable()) yield $prop->getName() => $prop->getValue();
        }
    }

    /** {@inheritdoc} */
    public function __isset($name) {

        return $this->offsetExists($name);
    }

    /** {@inheritdoc} */
    public function __get($name) {

        return $this->offsetGet($name);
    }

    /** {@inheritdoc} */
    public function __set($name, $value) {
        $this->offsetSet($name, $value);
    }

    /** {@inheritdoc} */
    public function __unset($name) {
        $this->offsetUnset($name);
    }

    /** {@inheritdoc} */
    public function __clone() {
        $properties = array_keys($this->properties);
        foreach ($properties as $name) {
            $this->properties[$name] = clone $this->properties[$name];
        }
    }

}
