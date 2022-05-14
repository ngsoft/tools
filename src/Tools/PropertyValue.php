<?php

declare(strict_types=1);

namespace NGSOFT\Tools;

use Closure;

class PropertyValue {

    public const VALUE_DIRECT = 0;
    public const VALUE_GETTER = 1;
    public const VALUE_SETTER = 2;
    public const VALUE_BOTH = 3;

    /** @var mixed */
    private $value;

    /** @var int */
    private $valueType;

    /** @var ?callable */
    private $getter;

    /** @var ?callable */
    private $setter;

    public function __construct(
            mixed $value
    ) {

        $valueType = self::VALUE_DIRECT;

        if (is_array($value)) {
            if (is_callable($getter = $value['get'] ?? null)) {
                $valueType += self::VALUE_GETTER;
                $getter = Closure::fromCallable($getter);
                $this->getter = $getter;
            }
            if (is_callable($setter = $value['set'] ?? null)) {
                $valueType += self::VALUE_SETTER;
                $setter = Closure::fromCallable($setter);
                $this->setter = $setter;
            }
        }


        $this->valueType = $valueType;
    }

    public function getValue(): mixed {
        switch ($this->valueType) {
            case self::VALUE_GETTER:
            case self::VALUE_BOTH:
                $value = call_user_func($this->getter);
                break;

            default :
                $value = $this->value;
        }

        return $value;
    }

    public function setValue(mixed $value): void {

        if (in_array($this->valueType, [self::VALUE_SETTER, self::VALUE_BOTH])) {
            call_user_func($this->setter, $value);
        } elseif ($this->valueType === self::VALUE_DIRECT) $this->value = $value;
    }

}
