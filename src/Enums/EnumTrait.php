<?php

declare(strict_types=1);

namespace NGSOFT\Enums;

use BackedEnum,
    BadMethodCallException,
    InvalidArgumentException,
    Throwable,
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
        return $this->value;
    }

    /** {@inheritdoc} */
    final public static function __callStatic(string $name, array $arguments): mixed
    {
        if (count($arguments) > 0) {
            throw new InvalidArgumentException(sprintf('Too many arguments for method %s::%s()', static::class, $name));
        }
        try {
            return static::get($name);
        } catch (Throwable) {
            throw new BadMethodCallException(sprintf('Invalid method %s::%s()', static::class, $name));
        }
    }

    private function isEnum(): bool
    {
        return is_subclass_of($this, BackedEnum::class) || is_subclass_of($this, Enum::class);
    }

    /**
     * Get Enum Value
     * @return int|string|null
     */
    public function getValue(): int|string|null
    {
        if ( ! $this->isEnum()) {
            return null;
        }
        return $this->value;
    }

    /**
     * Get Enum Name
     * @return string
     */
    public function getName(): string
    {
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
            if ($input instanceof self) return static::class === $input::class && $input->value === $this->value;
            return $input === $this->value;
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
            if ($enum->name === $name) {
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

        return self::from($enum);
    }

}
