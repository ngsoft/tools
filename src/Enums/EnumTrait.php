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
 * A trait to use with enums
 *
 *
 * @phan-file-suppress PhanTypeMismatchReturn,PhanTypeMismatchDeclaredParam,PhanAbstractStaticMethodCallInTrait
 */
trait EnumTrait
{

    public readonly string $name;
    public readonly int|string $value;

    /**
     * Generates a list of cases on an enum
     * This method will return a packed array of all cases in an enumeration, in lexical order.
     *
     * @phan-suppress PhanParamTooManyInternal, PhanTypeInstantiateAbstractStatic
     * @return static[] An array of all defined cases of this enumeration, in lexical order.
     */
    abstract public static function cases(): array;

    /** {@inheritdoc} */
    public function __toString(): string
    {
        return (string) $this->value;
    }

    /** {@inheritdoc} */
    public function jsonSerialize(): mixed
    {
        return $this->value;
    }

    /** {@inheritdoc} */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        if (count($arguments) > 0) throw new InvalidArgumentException(sprintf('Too many arguments for method %s::%s()', static::class, $name));
        try {
            return static::get($name);
        } catch (Throwable) {
            throw new BadMethodCallException(sprintf('Invalid method %s::%s()', static::class, $name));
        }
    }

    /**
     * Checks if current Enum is one of the inputs
     *
     * @param Enum|BackedEnum|int|string $input
     * @return bool
     */
    public function is(self|int|string ...$input): bool
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
     * Checks if enum is defined
     *
     * @param string $name
     * @return bool
     */
    final public static function has(string $name): bool
    {
        return static::tryGet($name) !== null;
    }

}
