<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Objects;

class stdObjectIterator extends \ArrayIterator {

    /** @var string */
    private $stdObjectClass;

    /**
     * Imports an Array into the iterator
     * @param array $array
     * @param string $classname A class extending stdObject
     * @param int $flags flags from \ArrayIterator
     */
    public function __construct(array $array = array(), string $classname = stdObject::class, int $flags = 0) {
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
