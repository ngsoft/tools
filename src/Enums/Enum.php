<?php

declare(strict_types=1);

namespace NGSOFT\Enums;

use JsonSerializable,
    LogicException,
    ReflectionClass,
    ReflectionClassConstant,
    Stringable,
    TypeError,
    ValueError;
use function get_debug_type;

/**
 * Basic Enum Class Support
 * Adds the ability to class constants to work as php 8.1 backed enums cases
 */
abstract class Enum implements Stringable, JsonSerializable
{

    use EnumTrait;

    protected const ERROR_ENUM_DUPLICATE_VALUE = 'Duplicate value in enum %s for cases %s and %s';
    protected const ERROR_ENUM_TYPE = 'Enum %s::%s case type %s does not match enum type string|int';
    protected const ERROR_ENUM_VALUE = '"%s" is not a valid value for enum "%s"';
    protected const IS_VALID_ENUM_NAME = '#^[A-Z](?:[\w+]+[A-Z0-9a-z])?$#';

    final protected function __construct(
            string $name,
            int|string $value
    )
    {
        $this->name = $name;
        $this->value = $value;
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
        if (!isset($instances[$className])) {

            $instances[$className] = [];

            $inst = &$instances[$className];

            $defined = $values = [];

            /** @var ReflectionClass $reflector */
            $reflector = new ReflectionClass($className);
            if ($reflector->isAbstract()) throw new LogicException(sprintf('Cannot initialize abstract Enum %s', $className));

            do {
                if ($reflector->getName() === Enum::class) break;

                $reflClassName = $reflector->getName();

                /** @var ReflectionClassConstant $classConstant */
                foreach ($reflector->getReflectionConstants(ReflectionClassConstant::IS_PUBLIC) as $classConstant) {
                    $name = $classConstant->getName();
                    $value = $classConstant->getValue();

                    if (isset($defined[$name])) continue;
                    if (!preg_match(self::IS_VALID_ENUM_NAME, $name)) continue;

                    if (!is_string($value) && !is_int($value)) {
                        throw new TypeError(sprintf(self::ERROR_ENUM_TYPE, $reflClassName, $name, get_debug_type($value)));
                    }

                    if (false !== ($key = array_search($value, $values, true))) {
                        throw new LogicException(sprintf(self::ERROR_ENUM_DUPLICATE_VALUE, $className, $key, $name));
                    }
                    $inst[] = new static($name, $value);
                    $defined[$name] = $name;
                    $values[$name] = $value;
                }
            } while (($reflector = $reflector->getParentClass()) instanceof ReflectionClass);
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
    public static function from(int|string $value): static
    {
        if ($result = static::tryFrom($value)) return $result;
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
    public static function tryFrom(int|string $value): ?static
    {
        if ($value instanceof self) {
            $value = $value->value;
        }

        /** @var static $instance */
        foreach (self::cases() as $instance) {
            if ($instance->value === $value) return $instance;
        }
        return null;
    }

    /** {@inheritdoc} */
    public function __serialize(): array
    {
        return [$this->name, $this->value];
    }

    /** {@inheritdoc} */
    public function __unserialize(array $data): void
    {
        list($this->name, $this->value) = $data;
    }

    /** {@inheritdoc} */
    public function __debugInfo(): array
    {
        return [
            sprintf('enum(%s::%s)', static::class, $this->name)
        ];
    }

}
