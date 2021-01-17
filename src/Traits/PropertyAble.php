<?php

declare(strict_types=1);

namespace NGSOFT\Traits;

use EmptyIterator,
    NGSOFT\Tools\Property,
    RuntimeException;

/**
 * Create Public Properties that are exposed to the user
 *
 * implements IteratorAggregate, Countable, ArrayAccess
 */
trait PropertyAble {

    /**
     * Set this flag to false to only add properties
     * using internal method setProperty()
     * or after creating properties using $this->prop='value';
     * to prevent new ones to be added
     * @var bool
     */
    protected $lazyProperties = true;

    /**
     * Set this flag to true to prevent throwing RuntimeException
     * TypeErrors will be thrown normally
     * @var bool
     */
    protected $silentProperties = false;

    /** @var array<string,\NGSOFT\Tools\Property> */
    private $properties = [];

    ////////////////////////////   Helpers   ////////////////////////////

    final protected function getProperty(string $name): ?Property {
        return $this->properties[$name] ?? null;
    }

    /**
     * Get Property value
     * @param string $name
     * @return mixed|null
     */
    final protected function getPropertyValue(string $name) {
        if ($property = $this->getProperty($name)) return $property->getValue();
        return null;
    }

    /**
     * Set a Property Value
     * @param string $name
     * @param mixed $value
     * @return static
     */
    final protected function setPropertyValue(
            string $name,
            $value
    ): self {

        try {
            if ($property = $this->getProperty($name)) {
                $property->setValue($value);
            } elseif ($this->lazyProperties) return $this->setProperty($name, $value, true, true, true);
            else throw new RuntimeException(sprintf('Property "%s" does not exists.', $name));
        } catch (RuntimeException $error) {
            if (!$this->silentProperties) throw $error;
        }
        return $this;
    }

    /**
     * Add a property
     * @param string $name
     * @param mixed $value
     * @param bool $writable
     * @param bool $configurable
     * @param bool $enumerable
     * @return static
     * @throws RuntimeException
     */
    final protected function setProperty(
            string $name,
            $value,
            bool $writable = true,
            bool $configurable = true,
            bool $enumerable = false
    ): self {

        try {
            if (
                    ($current = $this->getProperty($name)) and
                    !$current->isConfigurable()
            ) throw new RuntimeException(sprintf('Property "%s" is not configurable, it cannot be set twice', $name));
            else $this->properties[$name] = new Property($this, $name, $value, $configurable, $enumerable, $writable);
        } catch (RuntimeException $error) {
            if (!$this->silentProperties) throw $error;
        }
        return $this;
    }

    /**
     * Removes a property
     * @param string $name
     * @return static
     * @throws RuntimeException
     */
    final protected function removeProperty(string $name): self {
        try {
            if ($property = $this->getProperty($name)) {
                if (!$property->isConfigurable()) {
                    throw new RuntimeException(sprintf('Property "%s" is not configurable, it cannot be removed.', $name));
                } else unset($this->properties[$name]);
            }
        } catch (RuntimeException $error) {
            if (!$this->silentProperties) throw $error;
        }
        return $this;
    }

    ////////////////////////////   Magic Methods   ////////////////////////////

    /** {@inheritdoc} */
    final public function __set($name, $value) {
        $this->setPropertyValue($name, $value);
    }

    /** {@inheritdoc} */
    final public function __get($name) {
        return $this->getPropertyValue($name);
    }

    /** {@inheritdoc} */
    final public function __isset($name) {
        return isset($this->properties[$name]);
    }

    final public function __unset($name) {
        $this->removeProperty($name);
    }

    ////////////////////////////   Iterator   ////////////////////////////

    /** {@inheritdoc} */
    final public function count() {
        return count(array_filter($this->properties, fn($prop) => $prop->isEnumerable()));
    }

    /** {@inheritdoc} */
    final public function getIterator() {
        if ($this->count() > 0) {
            foreach ($this->properties as $prop) {
                if ($prop->isEnumerable()) yield $prop->getName() => $prop->getValue();
            }
        } else return new EmptyIterator();
    }

    ////////////////////////////   ArrayAccess   ////////////////////////////

    /** {@inheritdoc} */
    final public function offsetExists($offset) {
        return is_string($offset) ? isset($this->properties[$offset]) : false;
    }

    /** {@inheritdoc} */
    final public function offsetGet($offset) {

        return is_string($offset) ? $this->getPropertyValue($offset) : null;
    }

    /** {@inheritdoc} */
    final public function offsetSet($offset, $value) {
        if (is_string($offset)) {
            $this->setPropertyValue($offset, $value);
        }
    }

    /** {@inheritdoc} */
    final public function offsetUnset($offset) {
        if (is_string($offset)) {
            $this->removeProperty($offset);
        }
    }

    ////////////////////////////   CloneAble   ////////////////////////////

    /** {@inheritdoc} */
    final public function __clone() {
        foreach (array_keys($this->properties)as $name) {
            $clone = clone $this->properties[$name];
            $clone->bindTo($this);
            $this->properties[$name] = $clone;
        }
    }

    public function __debugInfo() {
        return $this->properties;
    }

}
