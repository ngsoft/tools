<?php

declare(strict_types=1);

namespace NGSOFT\Tools;

use Closure,
    InvalidArgumentException,
    RuntimeException;

final class Property
{

    private const VALID_PROPERTY_NAME = '/^[a-z_][\w\-]+/i';
    public const PROPERTY_TYPE_NONE = 0;
    public const PROPERTY_TYPE_READONLY = 1;
    public const PROPERTY_TYPE_BOTH = 2;

    /** @var string */
    private $name;

    /** @var mixed */
    private $value;

    /** @var int */
    private $type;

    /** @var callable */
    private $getter;

    /** @var callable */
    private $setter;

    public function __construct(
            string $name,
            mixed $value = null,
            private bool $configurable = true,
            private bool $enumerable = true,
            private bool $writable = true,
    )
    {
        if ( ! preg_match(self::VALID_PROPERTY_NAME, $name)) throw new InvalidArgumentException(sprintf('Invalid property name "%s"', $name));
        $this->name = $name;
        $type = 0;
        $getter = $setter = null;
        if (is_array($value)) {
            $getter = is_callable($value['get'] ?? null) ? Closure::fromCallable($value['get']) : null;
            $setter = is_callable($value['set'] ?? null) ? Closure::fromCallable($value['set']) : null;
        }


        if (is_callable($setter)) {
            $type = self::PROPERTY_TYPE_BOTH;
        } elseif (is_callable($getter)) $type = self::PROPERTY_TYPE_READONLY;

        if ($type === self::PROPERTY_TYPE_NONE) $this->value = $value;

        $this->type = $type;

        $getter = $getter ?? Closure::fromCallable(function () { return $this->value; });
        $setter = $setter ?? Closure::fromCallable(function (mixed $value): void { $this->value = $value; });

        $this->getter = $getter;
        $this->setter = $setter;
    }

    public function bindTo(object $bindTo): void
    {
        foreach (['getter', 'setter'] as $prop) {
            $this->{$prop} = \Closure::bind($this->{$prop}, $bindTo, get_class($bindTo));
        }
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getWritable(): bool
    {
        return $this->writable;
    }

    public function getConfigurable(): bool
    {
        return $this->configurable;
    }

    public function getEnumerable(): bool
    {
        return $this->enumerable;
    }

    public function getValue(): mixed
    {

        return call_user_func($this->getter);
    }

    public function setValue(mixed $value): void
    {

        if (
                ! $this->getWritable() &&
                $this->type !== self::PROPERTY_TYPE_BOTH
        ) {
            throw new RuntimeException(sprintf('Property %s is not writable.', $this->getName()));
        }

        if ($this->type === self::PROPERTY_TYPE_READONLY) {
            throw new RuntimeException(sprintf('Property "%s" is read only.', $this->getName()));
        }
        call_user_func($this->setter, $value);
    }

    public function __clone(): void
    {

        if ($this->type == self::PROPERTY_TYPE_NONE) {
            $this->getter = Closure::bind($this->getter, $this, static::class);
            $this->setter = Closure::bind($this->setter, $this, static::class);
        }
    }

    public function __debugInfo(): array
    {
        return [
            'configurable' => $this->getConfigurable(),
            'writable' => $this->getWritable(),
            'enumerable' => $this->getEnumerable(),
            'value' => $this->getValue()
        ];
    }

}
