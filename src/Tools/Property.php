<?php

declare(strict_types=1);

namespace NGSOFT\Tools;

use InvalidArgumentException;

class Property {

    private const VALID_PROPERTY_NAME = '/^[a-z_][\w\-]+/i';

    /** @var string */
    private $name;

    /** @var PropertyValue */
    private $value;

    public function __construct(
            string $name,
            mixed $value = null,
            private bool $configurable = true,
            private bool $enumerable = false,
            private bool $writable = false,
    ) {
        if (!preg_match(self::VALID_PROPERTY_NAME, $name)) throw new InvalidArgumentException(sprintf('Invalid properrty name "%s"', $name));
        $this->value = $value instanceof PropertyValue ? $value : new PropertyValue($value);
    }

    public function getName(): string {
        return $this->name;
    }

    public function getWritable(): bool {
        return $this->writable;
    }

    public function getConfigurable(): bool {
        return $this->configurable;
    }

    public function getEnumerable(): bool {
        return $this->enumerable;
    }

    public function getValue(): mixed {
        return $this->value->getValue();
    }

    public function setValue(mixed $value): void {
        $this->value->setValue($value);
    }

}
