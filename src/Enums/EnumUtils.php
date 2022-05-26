<?php

declare(strict_types=1);

namespace NGSOFT\Enums;

use InvalidArgumentException;

class EnumUtils {

    protected static function assertValidClassname(string $className) {
        if (!class_exists($className) || !in_array(Enum::class, class_parents($className))) {
            throw new InvalidArgumentException(sprintf('Enum Class %s does not exists or does not extends %s', $className, Enum::class));
        }
    }

    /**
     * Generates Doc Comment for magic static methods
     *
     *
     * @param string $className
     * @return string
     */
    public static function generateEnumClassPhpDoc(string $className): string {

        $contents = '';
        static::assertValidClassname($className);

        $reflector = new \ReflectionClass($className);
        $currentDocs = $reflector->getDocComment();
        if (false === $currentDocs) $currentDocs = '';

        foreach ($className::cases() as $enum) {
            $newLine = sprintf(" * @method static static %s()", $enum->name);

            if (!str_contains($currentDocs, $newLine)) $contents .= "$newLine\n";
        }

        return $contents;
    }

    /**
     * Auto Generates static methods doc blocks for enums
     *
     * @param string $className
     * @return bool
     */
    public static function addPhpDocToEnumClass(string $className): bool {

        $contents = static::generateEnumClassPhpDoc($className);

        if (empty($contents)) return false;

        $reflector = new \ReflectionClass($className);
        $split = explode('\\', $className);
        $relClassName = array_pop($split);
        $docs = $reflector->getDocComment();
        $orig = $docs;
        // empty doc block
        if ($docs === false) $docs = "/**\n *\n */";

        if (!str_contains($docs, $contents)) {
            $result = [];
            $lines = explode("\n", $docs);
            foreach ($lines as $line) {
                if (str_contains($line, '*/')) {
                    $result[] = rtrim($contents);
                }
                $result[] = $line;
            }

            $docs = implode("\n", $result);

            $fName = $reflector->getFileName();

            if ($classContents = file_get_contents($fName)) {


                if ($orig === false) {
                    foreach (explode("\n", $classContents) as $fileLine) {
                        if (str_contains($fileLine, 'class ') && str_contains($fileLine, $relClassName)) {
                            $newContents = str_replace($fileLine, "$docs\$fileLine", $classContents);
                        }
                    }
                } else $newContents = str_replace($orig, $docs, $classContents);

                if (isset($newContents)) {
                    return file_put_contents($fName, $newContents) > 0;
                }
            }
        }
        return false;
    }

}
