<?php

declare(strict_types=1);

namespace NGSOFT\Enums;

use InvalidArgumentException,
    JsonSerializable,
    LogicException,
    NGSOFT\RegExp,
    ReflectionClass,
    ReflectionClassConstant,
    Stringable,
    TypeError,
    ValueError;

/**
 * Basic Enum Support
 */
abstract class Enum implements Stringable, JsonSerializable {

    protected const ERROR_ENUM_DUPLICATE_VALUE = 'Duplicate value in enum %s for cases %s and %s';
    protected const ERROR_ENUM_TYPE = 'Enum %s::%s case type %s does not match enum type string|int|float';
    protected const ERROR_ENUM_CASE = 'Enum %s::%s does not exists.';
    protected const ERROR_ENUM_VALUE = '"%s" is not a valid value for enum "%s"';
    protected const IS_VALID_ENUM_NAME = '^[A-Z](?:[\w+]+[A-Z0-9a-z])?$';

    private static array $instances = [];
    private static array $indexes = [];

    final protected function __construct(
            public readonly string $name,
            public readonly int|float|string $value
    ) {

    }

    /**
     * Get Enum instance by name
     *
     * @param string $name
     * @return static
     * @throws ValueError
     */
    public static function get(string $name): static {
        static::cases();
        $index = self::$indexes[static::class][$name] ?? null;
        if (!is_int($index)) throw new ValueError(sprintf(self::ERROR_ENUM_CASE, static::class, $name));
        return self::$instances[static::class][$index];
    }

    /**
     * Checks if enum is defined
     *
     * @param string $name
     * @return bool
     */
    final public static function has(string $name): bool {
        return is_int(self::$indexes[static::class][$name] ?? null);
    }

    /**
     * Generates a list of cases on an enum
     * This method will return a packed array of all cases in an enumeration, in lexical order.
     *
     * @phan-suppress PhanParamTooManyInternal, PhanTypeInstantiateAbstractStatic
     * @return array An array of all defined cases of this enumeration, in lexical order.
     * @throws TypeError
     * @throws LogicException
     */
    final public static function cases(): array {
        /** @var RegExp $isValidName */
        static $isValidName;
        $isValidName = $isValidName ?? RegExp::create(self::IS_VALID_ENUM_NAME);
        $instances = &self::$instances;
        $indexes = &self::$indexes;
        $className = static::class;
        if (!isset($instances[$className])) {
            $instances[$className] = $indexes[$className] = [];

            $inst = &$instances[$className];
            $ids = &$indexes[$className];

            $defined = $values = [];
            $index = 0;

            /** @var ReflectionClass $reflector */
            $reflector = new ReflectionClass($className);
            if ($reflector->isAbstract()) throw new LogicException(sprintf('Cannot initialize abstract Enum %s', $className));


            do {
                if ($reflector->getName() === self::class) break;


                $reflClassName = $reflector->getName();

                /** @var ReflectionClassConstant $classConstant */
                foreach ($reflector->getReflectionConstants(ReflectionClassConstant::IS_PUBLIC) as $classConstant) {
                    $name = $classConstant->getName();
                    $value = $classConstant->getValue();

                    if (isset($defined[$name])) continue;
                    if (!$isValidName->test($name)) continue;

                    if (!is_string($value) && !is_int($value) && !is_float($value)) {
                        throw new TypeError(sprintf(self::ERROR_ENUM_TYPE, $reflClassName, $name, get_debug_type($value)));
                    }

                    if (false !== ($key = array_search($value, $values, true))) {
                        throw new LogicException(sprintf(self::ERROR_ENUM_DUPLICATE_VALUE, $className, $key, $name));
                    }

                    $inst[$index] = new static($name, $value);
                    $ids[$name] = $index;
                    $defined[$name] = $name;
                    $values[$name] = $value;
                    $index++;
                }
            } while (($reflector = $reflector->getParentClass()) instanceof ReflectionClass);
        }

        return $instances[$className];
    }

    /**
     * Maps a scalar to an enum instance
     * The from() method translates a float,string or int
     * into the corresponding Enum case, if any. If there is no matching case defined, it will throw a ValueError.
     *
     * @param int|string|float $value
     * @return static
     * @throws ValueError
     */
    public static function from(int|string|float $value): static {
        if ($result = static::tryFrom($value)) return $result;
        throw new ValueError(sprintf(self::ERROR_ENUM_VALUE, (string) $value, static::class));
    }

    /**
     * Maps a scalar to an enum instance or null
     * The tryFrom() method translates a float, string or int into the corresponding Enum case,
     * if any. If there is no matching case defined, it will return null.
     *
     * @param int|string|float $value
     * @return static|null
     */
    public static function tryFrom(int|string|float $value): ?static {
        /** @var static $instance */
        foreach (self::cases() as $instance) {
            if ($instance->value === $value) return $instance;
        }
        return null;
    }

    /** {@inheritdoc} */
    public static function __callStatic(string $name, array $arguments): mixed {
        if (count($arguments) > 0) throw new InvalidArgumentException(sprintf('Too many arguments for method %s::%s()', static::class, $name));
        try {
            return static::get($name);
        } catch (\Throwable) {
            throw new BadMethodCallException(sprintf('Invalid method %s::%s()', static::class, $name));
        }
    }

    /** {@inheritdoc} */
    public function __serialize(): array {
        return [$this->name, $this->value];
    }

    /** {@inheritdoc} */
    public function __unserialize(array $data) {
        list($this->name, $this->value) = $data;
    }

    /** {@inheritdoc} */
    public function __toString(): string {
        return (string) $this->value;
    }

    /** {@inheritdoc} */
    public function jsonSerialize(): mixed {
        return $this->value;
    }

    /** {@inheritdoc} */
    public function __debugInfo(): array {
        return [
            sprintf('enum(%s::%s)', static::class, $this->name)
        ];
    }

}
