<?php

declare(strict_types=1);

namespace NGSOFT\Tools;

use ArrayAccess,
    Countable,
    IteratorAggregate,
    NGSOFT\Attributes\HasProperties,
    ReflectionClass,
    ReflectionException,
    RuntimeException,
    Throwable,
    Traversable;

#[HasProperties]
class PropertyAble implements ArrayAccess, Countable, IteratorAggregate
{

    /** @var Property[] */
    protected $properties = [];

    /**
     * @phan-suppress PhanPossiblyInfiniteLoop
     * @param object $instance
     * @return \Traversable
     */
    private static function getClassParents(object $instance): \Traversable
    {
        try {

            $reflector = null;

            while (($reflector = is_null($reflector) ? new \ReflectionClass($instance) : $reflector->getParentClass() ) !== false) {
                yield $reflector;
            }
        } catch (ReflectionException) {

        }
    }

    public static function getMetadatas(object $instance): HasProperties
    {
        static $metadata = [];
        $className = $instance::class;
        if ( ! isset($metadata[$className])) {
            $meta = new HasProperties();
            /** @var ReflectionClass $reflClass */
            foreach (self::getClassParents($instance) as $reflClass) {
                $attrs = $reflClass->getAttributes(HasProperties::class, \ReflectionAttribute:: IS_INSTANCEOF);
                if (empty($attrs)) continue;
                foreach ($attrs as $attr) {
                    $meta = $attr->newInstance();
                    break 2;
                }
            }
            $metadata[$className] = $meta;
        }
        return $metadata[$className];
    }

    protected function defineProperty(
            string $name,
            mixed $value,
            bool $enumerable = true,
            bool $configurable = true,
            bool $writable = true
    ): static
    {
        /** @var Property $current */
        if ($current = $this->properties[$name] ?? null) {
            if ( ! $current->getConfigurable()) throw new RuntimeException(sprintf('Cannot define property "%s": not configurable.', $name));
        }

        $meta = static::getMetadatas($this);

        try {
            $this->properties[$name] = new Property($name, $value, configurable: $configurable, enumerable: $enumerable, writable: $writable);
        } catch (Throwable $error) {
            if ( ! $meta->silent) throw $error;
        }

        return $this;
    }

    protected function removeProperty(string $name): static
    {
        try {
            /** @var Property $current */
            if ($current = $this->properties[$name]) {
                if ( ! $current->getConfigurable()) {

                    throw new RuntimeException(sprintf('Cannot remove property "%s": not configurable.', $name));
                }

                unset($this->properties[$name]);
            }
        } catch (Throwable $error) {
            if ( ! static::getMetadatas($this)->silent) throw $error;
        }


        return $this;
    }

    private function getPropertyValue(string $name): mixed
    {
        $value = null;
        if ($current = $this->properties[$name] ?? null) {
            $value = $current->getValue();
        }

        return $value;
    }

    private function setPropertyValue(string $name, mixed $value): void
    {

        try {
            if ($current = $this->properties[$name] ?? null) {
                $current->setValue($value);
                return;
            }
            if ( ! static::getMetadatas($this)->lazy) throw new RuntimeException(sprintf('Cannot create property "%s"', $name));
            $this->defineProperty($name, $value, enumerable: true);
        } catch (Throwable $error) {
            if ( ! static::getMetadatas($this)->silent) throw $error;
        }
    }

    /** {@inheritdoc} */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->properties[$offset]);
    }

    /** {@inheritdoc} */
    public function offsetGet(mixed $offset): mixed
    {

        return $this->getPropertyValue($offset);
    }

    /** {@inheritdoc} */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->setPropertyValue($offset, $value);
    }

    /** {@inheritdoc} */
    public function offsetUnset(mixed $offset): void
    {
        $this->removeProperty($offset);
    }

    /** {@inheritdoc} */
    public function count(): int
    {

        return count($this->properties);
    }

    /** {@inheritdoc} */
    public function getIterator(): Traversable
    {
        /** @var Property $prop */
        foreach ($this->properties as $prop) {
            if ($prop->getEnumerable()) yield $prop->getName() => $prop->getValue();
        }
    }

    /** {@inheritdoc} */
    public function __isset(string $name): bool
    {

        return $this->offsetExists($name);
    }

    /** {@inheritdoc} */
    public function __get(string $name): mixed
    {

        return $this->offsetGet($name);
    }

    /** {@inheritdoc} */
    public function __set(string $name, mixed $value): void
    {
        $this->offsetSet($name, $value);
    }

    /** {@inheritdoc} */
    public function __unset(string $name): void
    {
        $this->offsetUnset($name);
    }

    /** {@inheritdoc} */
    public function __clone(): void
    {
        $properties = array_keys($this->properties);
        foreach ($properties as $name) {
            $clone = clone $this->properties[$name];

            if ($clone->getType() === Property::PROPERTY_TYPE_BOTH) {
                $clone->bindTo($this);
            }

            $this->properties[$name] = $clone;
        }
    }

}
