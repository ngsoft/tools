<?php

declare(strict_types=1);

namespace NGSOFT\Enums;

use InvalidArgumentException;

class EnumUtils {

    public static function generateEnumClassPhpDoc(string $className): string {

        $contents = '';

        if (!class_exists($className) || !in_array(Enum::class, class_parents($className))) {
            throw new InvalidArgumentException(sprintf('Enum Class %s does not exists or does not extends %s', $className, Enum::class));
        }

        foreach ($className::cases() as $enum) {
            $contents .= sprintf("* @method static static %s()\n", $enum->name);
        }

        return $contents;
    }

}
