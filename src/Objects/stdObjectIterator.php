<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Objects;

class stdObjectIterator extends \ArrayIterator {

    /** @var string */
    private $stdObjectClass;

    public function __construct($array = array(), $classname = stdObject::class, int $flags = 0) {
        if (class_exists($classname) and in_array(stdObject::class, class_parents($classname))) $this->stdObjectClass = $classname;
        else $this->stdObjectClass = stdObject::class;
        parent::__construct($array, $flags);
    }

    public function current() {
        $value = parent::current();
        $value = is_array($value) ? $this->stdObjectClass::from($value) : $value;
        return $value;
    }

}
