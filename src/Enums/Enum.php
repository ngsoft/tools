<?php

declare(strict_types=1);

namespace NGSOFT\Enums;

use JsonSerializable,
    LogicException,
    Throwable,
    TypeError,
    ValueError;
use function get_class_constants,
             get_debug_type;

/**
 * Basic Enum Class Support (Polyfill)
 * Adds the ability to class constants to work as php 8.1 backed enums cases
 */
abstract class Enum implements JsonSerializable
{

    use EnumTrait;

    protected const ERROR_ENUM_DUPLICATE_VALUE = 'Duplicate value %s in enum %s for cases %s and %s';
    protected const ERROR_ENUM_TYPE = 'enum(%s::%s) case type %s does not match enum type string|int';
    protected const ERROR_ENUM_VALUE = '"%s" is not a valid value for enum "%s"';
    protected const ERROR_ENUM_MULTITYPE = 'enum(%s::%s) case type %s does not match enum backing type %s';
    protected const IS_VALID_ENUM_NAME = '#^[A-Z](?:[\w+]+[A-Z0-9a-z])?$#';
    protected const NO_MAGIC = 'enum %s may not include %s';

    private function __construct(
            public readonly string $name,
            public readonly int|string $value
    )
    {

        static $tested = [], $diallowed = [
            '__get',
            '__set',
            '__destruct',
            '__clone',
            '__sleep',
            '__wakeup',
            '__set_state'
        ];

        if ( ! isset($tested[static::class])) {
            foreach ($diallowed as $method) {
                if (method_exists($this, $method)) {
                    throw new LogicException(sprintf(self::NO_MAGIC, static::class, $method));
                }
            }

            $tested[static::class] = static::class;
        }
    }

    /**
     * Generates a list of cases on an enum
     * This method will return a packed array of all cases in an enumeration, in lexical order.
     *
     * @phan-suppress PhanParamTooManyInternal, PhanTypeInstantiateAbstractStatic
     * @return static[] An array of all defined cases of this enumeration, in lexical order.
     * @throws TypeError
     * @throws LogicException
     */
    final public static function cases(): array
    {
        /** @var array<string, static[]> $instances */
        static $instances = [];

        $className = static::class;

        if ( ! isset($instances[$className])) {

            $instances[$className] = [];

            $enums = &$instances[$className];

            $values = $defined = [];
            $previous = null;

            foreach (get_class_constants($className) as $name => $value) {
                if (
                        ! preg_match(self::IS_VALID_ENUM_NAME, $name) ||
                        isset($defined[$name])
                ) {
                    continue;
                }

                if ( ! is_string($value) && ! is_int($value)) {
                    throw new TypeError(sprintf(self::ERROR_ENUM_TYPE, $className, $name, get_debug_type($value)));
                }

                if (is_null($previous)) {
                    $previous = get_debug_type($value);
                }

                if (get_debug_type($value) !== $previous) {
                    throw new TypeError(sprintf(self::ERROR_ENUM_MULTITYPE, $className, $name, get_debug_type($value), $previous));
                }

                if ($key = array_search($value, $values, true)) {
                    throw new LogicException(sprintf(self::ERROR_ENUM_DUPLICATE_VALUE, (string) $value, $className, $key, $name));
                }


                $enums[] = new static($name, $value);
                $defined[$name] = $name;
                $values[$name] = $value;
            }
        }

        return $instances[$className];
    }

    /**
     * Maps a scalar to an enum instance
     * The from() method translates a string or int
     * into the corresponding Enum case, if any. If there is no matching case defined, it will throw a ValueError.
     *
     * @param int|string $value
     * @return static
     * @throws ValueError
     */
    final public static function from(int|string $value): static
    {
        /** @var static $instance */
        foreach (self::cases() as $enum) {
            if ($enum->value === $value) return $enum;
        }

        throw new ValueError(sprintf(self::ERROR_ENUM_VALUE, (string) $value, static::class));
    }

    /**
     * Maps a scalar to an enum instance or null
     * The tryFrom() method translates a string or int into the corresponding Enum case,
     * if any. If there is no matching case defined, it will return null.
     *
     * @param int|string $value
     * @return static|null
     */
    final public static function tryFrom(int|string $value): ?static
    {
        try {
            return static::from($value);
        } catch (Throwable) {
            return null;
        }
    }

    /** {@inheritdoc} */
    final public function __serialize(): array
    {
        return [$this->name, $this->value];
    }

    /** {@inheritdoc} */
    final public function __unserialize(array $data): void
    {
        list($this->name, $this->value) = $data;
    }

    /** {@inheritdoc} */
    final public function __debugInfo(): array
    {
        return [
            sprintf('enum(%s::%s)', static::class, $this->name) => $this->value
        ];
    }

}
