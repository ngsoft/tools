<?php

declare(strict_types=1);

namespace NGSOFT\Tools;

use Closure,
    InvalidArgumentException,
    RuntimeException;

final class Property {

    const VALID_NAME_REGEX = '/^[a-z][\w\-]+/i';

    /** @var string */
    private $name;

    /** @var bool */
    private $writable = false;

    /** @var bool */
    private $configurable = true;

    /** @var bool */
    private $enumerable = false;

    /** @var mixed */
    private $value;

    /** @var callable|null */
    private $get;

    /** @var callable|null */
    private $set;

    /**
     * Creates a new Property
     * @param object $bindTo
     * @param string $name
     * @param mixed $value
     * @param bool $configurable
     * @param bool $enumerable
     * @param bool $writable
     * @throws InvalidArgumentException
     */
    public function __construct(
            object $bindTo,
            string $name,
            $value,
            bool $configurable = true,
            bool $enumerable = false,
            bool $writable = false
    ) {
        $this->configurable = $configurable;
        $this->writable = $writable;
        $this->enumerable = $enumerable;
        if (!preg_match(self::VALID_NAME_REGEX, $name)) {
            throw new InvalidArgumentException(sprintf('Invalid property name "%s"', $name));
        }
        $this->name = $name;
        if (
                is_array($value)
        ) {
            $get = $value['get'] ?? null;
            $set = $value['set'] ?? null;
            if (
                    is_callable($get) or
                    is_callable($set)
            ) {
                //convert callables to closures
                $this->get = Closure::fromCallable(is_callable($get) ? $get : fn() => null);
                $this->set = Closure::fromCallable(is_callable($set) ? $set : fn() => null);
                //bind to instance
                $this->bindTo($bindTo);
            } else $this->value = $value;
        } else $this->value = $value;
    }

    /**
     * @return bool true if the value associated with the property may be changed with an assignment operator (ignored with getter or setter added).
     */
    public function isWritable(): bool {
        return $this->writable;
    }

    /**
     * @return bool true if the type of this property descriptor may be changed and if the property may be deleted from the corresponding object.
     */
    public function isConfigurable(): bool {
        return $this->configurable;
    }

    /**
     * Set the iterable flag
     * @return bool true if and only if this property shows up during enumeration of the properties on the corresponding object.
     */
    public function isEnumerable(): bool {
        return $this->enumerable;
    }

    /**
     * Get Property Name
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Get Value for Property
     * @return mixed
     */
    public function getValue() {
        if (is_callable($this->get)) {
            $callback = $this->get;
            return $callback();
        }
        return $this->value;
    }

    /**
     * Set Value for Property
     * @param mixed $value
     * @return void
     * @throws RuntimeException
     * @suppress PhanParamTooMany
     */
    public function setValue($value) {
        if (is_callable($this->set)) {
            $callback = $this->set;
            $callback($value);
        } elseif (!$this->writable) {
            throw new RuntimeException(sprintf('Property "%s" is not writable.', $this->name));
        } else $this->value = $value;
    }

    /**
     * Binds get and set callable to specified object
     * @param object $bindTo
     * @return static
     */
    public function bindTo(object $bindTo): self {
        foreach (['get', 'set'] as $prop) {
            if ($this->{$prop} instanceof Closure) {
                $closure = $this->{$prop};
                $this->{$prop} = Closure::bind($closure, $bindTo, get_class($bindTo));
            }
        }
        return $this;
    }

}
