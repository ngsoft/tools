<?php

declare(strict_types=1);

namespace NGSOFT\Enums;

use BackedEnum,
    BadMethodCallException,
    InvalidArgumentException,
    RuntimeException,
    Throwable,
    UnitEnum,
    ValueError;
use function NGSOFT\Tools\some;

/**
 * A trait to use with Enum/BackedEnum
 *
 * @phan-file-suppress PhanTypeMismatchReturn,PhanTypeMismatchDeclaredParam, PhanUndeclaredProperty
 */
trait EnumTrait
{

    /** {@inheritdoc} */
    public function jsonSerialize(): mixed
    {
        return $this->getValue();
    }

    /** {@inheritdoc} */
    final public static function __callStatic(string $name, array $arguments): mixed
    {

        // missing from  from \UnitEnum
        // to use fromEnum
        if ($name === 'from') {
            $enum = $arguments[0] ?? 0;
            if ( ! is_string($enum)) {
                throw new ValueError(sprintf('Cannot import enum %s with value.', static::class));
            }
            return static::get($enum);
        }

        try {

            if (count($arguments) > 0) {
                throw new InvalidArgumentException(sprintf('Too many arguments for method %s::%s()', static::class, $name));
            }
            return static::get($name);
        } catch (Throwable $prev) {
            throw new BadMethodCallException(sprintf('Invalid method %s::%s()', static::class, $name), previous: $prev);
        }
    }

    protected function isEnum(object $enum): bool
    {
        return is_subclass_of($enum, BackedEnum::class) || is_subclass_of($enum, Enum::class) || is_subclass_of($enum, UnitEnum::class);
    }

    /**
     * Get Enum Value
     * @return int|string
     */
    final public function getValue(): int|string
    {
        return $this->value ?? $this->getName();
    }

    /**
     * Get Enum Name
     * @return string
     */
    final public function getName(): string
    {
        if ( ! $this->isEnum($this)) {
            throw new RuntimeException(sprintf(
                                    'Trait %s can only be used in %s',
                                    EnumTrait::class,
                                    implode('|', [Enum::class, \BackedEnum::class, \UnitEnum::class]))
            );
        }


        return $this->name;
    }

    /**
     * Checks if current Enum is one of the inputs
     *
     * @param Enum|BackedEnum|int|string $input
     * @return bool
     */
    final public function is(self|int|string ...$input): bool
    {

        $compare = function ($input) {
            if ($input instanceof self) {
                return static::class === $input::class && $input->getValue() === $this->getValue();
            }
            return $input === $this->getValue();
        };

        return some($compare, $input);
    }

    /**
     * Get Enum instance by name
     *
     * @param string $name
     * @return static|null
     */
    final public static function tryGet(string $name): static|null
    {

        try {
            return static::get($name);
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Get Enum instance by name
     *
     * @param string $name
     * @return static
     * @throws ValueError
     */
    final public static function get(string $name): static
    {
        /** @var static $enum */
        foreach (static::cases() as $enum) {
            if ($enum->getName() === $name) {
                return $enum;
            }
        }
        throw new ValueError(sprintf('Enum %s::%s does not exists.', static::class, $name));
    }

    /**
     * Compatibility layer between real BackedEnum and polyfilled one
     * usage: MyEnum::fromEnum(MyEnum::MY_CASE)
     *
     * @param self|int|string $enum
     * @return static
     */
    final public static function fromEnum(self|int|string $enum): static
    {
        if ($enum instanceof self) {
            return $enum;
        }

        return static::from($enum);
    }

}
