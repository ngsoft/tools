<?php

declare(strict_types=1);

namespace NGSOFT\Enums;

use BackedEnum;

use function NGSOFT\Tools\some;

/**
 * A trait to use with Enum/BackedEnum.
 *
 * @phan-file-suppress PhanTypeMismatchReturn,PhanTypeMismatchDeclaredParam, PhanUndeclaredProperty, PhanTypeInvalidTraitParam
 */
trait EnumTrait
{
    final public static function __callStatic(string $name, array $arguments): mixed
    {
        // missing from \UnitEnum
        // to use fromEnum
        if ('from' === $name)
        {
            $enum = $arguments[0] ?? 0;

            if ( ! is_string($enum))
            {
                throw new \ValueError(sprintf('Cannot import enum %s with value.', static::class));
            }
            return static::get($enum);
        }

        try
        {
            if (count($arguments) > 0)
            {
                throw new \InvalidArgumentException(sprintf('Too many arguments for method %s::%s()', static::class, $name));
            }
            return static::get($name);
        } catch (\Throwable $prev)
        {
            throw new \BadMethodCallException(sprintf('Invalid method %s::%s()', static::class, $name), previous: $prev);
        }
    }

    public function jsonSerialize(): int|string
    {
        return $this->getValue();
    }

    /**
     * Get Enum Value.
     */
    final public function getValue(): int|string
    {
        return $this->value ?? $this->getName();
    }

    /**
     * Get Enum Name.
     */
    final public function getName(): string
    {
        if ( ! $this->isEnum($this))
        {
            throw new \RuntimeException(
                sprintf(
                    'Trait %s can only be used in %s',
                    EnumTrait::class,
                    implode('|', [Enum::class, \BackedEnum::class, \UnitEnum::class])
                )
            );
        }

        return $this->name;
    }

    /**
     * Checks if current Enum is one of the inputs.
     */
    final public function is(int|object|string ...$input): bool
    {
        $compare = function ($input)
        {
            if (is_object($input))
            {
                return static::class === $input::class && $input->getValue() === $this->getValue();
            }
            return $input === $this->getValue();
        };

        return some($compare, $input);
    }

    /**
     * Get Enum instance by name.
     */
    final public static function tryGet(string $name): null|static
    {
        try
        {
            return static::get($name);
        } catch (\Throwable)
        {
            return null;
        }
    }

    /**
     * Get Enum instance by name.
     *
     * @throws \ValueError
     */
    final public static function get(string $name): static
    {
        /** @var static $enum */
        foreach (static::cases() as $enum)
        {
            if ($enum->getName() === $name)
            {
                return $enum;
            }
        }
        throw new \ValueError(sprintf('Enum %s::%s does not exists.', static::class, $name));
    }

    /**
     * Compatibility layer between real BackedEnum and polyfill one
     * usage: MyEnum::fromEnum(MyEnum::MY_CASE).
     */
    final public static function fromEnum(int|object|string $enum): static
    {
        if ($enum instanceof self)
        {
            return $enum;
        }

        return static::from($enum);
    }

    protected function isEnum(object $enum): bool
    {
        return is_subclass_of($enum, \BackedEnum::class) || is_subclass_of($enum, Enum::class) || is_subclass_of($enum, \UnitEnum::class);
    }
}
