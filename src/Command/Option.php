<?php

namespace NGSOFT\Tools\Command;

class Option {

    const VALUE_NONE = 1;
    const VALUE_REQUIRED = 2;
    const VALUE_OPTIONAL = 3;

    /** @var string */
    private $long = "";

    /** @var string */
    private $short = "";

    /** @var int */
    private $flag;

    /** @var string */
    private $description = "";

    /** @var mixed|null */
    private $default;

    /** @var array */
    private $values = [];

    /**
     * @param string $long
     * @param string|null $short
     * @param int|null $flag
     * @param string|null $description
     * @param mixed|null $default
     */
    public function __construct(string $long, string $short = null, int $flag = self::VALUE_OPTIONAL, string $description = null, $default = null) {
        $long = preg_replace('/^[\-]+/', '', $long);
        $this->long = $long;
        if ($short !== null) {
            $short = preg_replace('/^[\-]+/', '', $short);
            $this->short = $short;
        }
        $flag === null || $this->flag = $flag;
        $description === null || $this->description = $description;
        $default === null || $this->default = $default;
    }

    public function getLong(): string {
        return $this->long;
    }

    public function getShort(): string {
        return $this->short;
    }

    public function setLong(string $long) {
        $this->long = $long;
        return $this;
    }

    public function setShort(string $short) {
        $this->short = $short;
        return $this;
    }

    public function getFlag(): int {
        return $this->flag;
    }

    public function setFlag(int $flag) {
        $this->flag = $flag;
        return $this;
    }

    public function getDescription(): string {
        return $this->description;
    }

    /** @return mixed */
    public function getDefault() {
        return $this->default;
    }

    public function setDescription(string $description) {
        $this->description = $description;
        return $this;
    }

    public function setDefault($default) {
        $this->default = $default;
        return $this;
    }

    /**
     * Get Parsed values for option
     * @return array
     */
    public function getValues(): array {
        return [];
    }

    /**
     * Get first value
     * @return mixed|null
     */
    public function getValue() {
        return array_key_exists(0, $this->values) ? $this->values[0] : null;
    }

    public function setValues(array $values) {
        $this->values = $values;
        return $this;
    }

    public function addValue($value) {
        $this->values[] = $value;
    }

}
