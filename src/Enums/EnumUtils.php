<?php

declare(strict_types=1);

namespace NGSOFT\Enums;

use BackedEnum,
    InvalidArgumentException,
    NGSOFT\RegExp,
    ReflectionClass;
use const NAMESPACE_SEPARATOR;
use function str_contains,
             uses_trait;

class EnumUtils
{

    protected static function assertValidClassname(string $className)
    {


        if (
                ! (is_subclass_of($className, BackedEnum::class) || is_subclass_of($className, Enum::class)) ||
                ! uses_trait($className, EnumTrait::class)
        ) {
            throw new InvalidArgumentException(sprintf(
                                    'enum(%s) does not exists, does not implements %s|%s or does not uses %s trait.',
                                    $className,
                                    Enum::class, BackedEnum::class,
                                    EnumTrait::class
            ));
        }
    }

    /**
     * Generates Doc Comment for magic static methods
     *
     *
     * @param string $className
     * @return string
     */
    public static function generateEnumClassPhpDoc(string $className): string
    {

        $contents = '';
        static::assertValidClassname($className);

        $reflector = new ReflectionClass($className);
        $currentDocs = $reflector->getDocComment();
        if (false === $currentDocs) $currentDocs = '';

        foreach ($className::cases() as $enum) {
            $newLine = sprintf(" * @method static static %s()", $enum->name);

            if ( ! str_contains($currentDocs, $newLine)) $contents .= "$newLine\n";
        }

        return $contents;
    }

    /**
     * Auto Generates static methods doc blocks for enums
     *
     * @param string $className
     * @return bool
     */
    public static function addPhpDocToEnumClass(string $className): bool
    {

        $isEnum = is_subclass_of($className, BackedEnum::class);

        $contents = static::generateEnumClassPhpDoc($className);

        if (empty($contents)) return false;

        $reflector = new ReflectionClass($className);
        $split = explode(NAMESPACE_SEPARATOR, $className);
        $relClassName = array_pop($split);
        if ( ! $isEnum) {
            $matchLine = RegExp::create(sprintf('class .*%s .*extends .*Enum', $relClassName));
        } else { $matchLine = RegExp::create(sprintf('enum .*%s.*:', $relClassName)); }


        $docs = $reflector->getDocComment();
        $orig = $docs;
        // empty doc block
        if ($docs === false) $docs = "/**\n */";

        if ( ! str_contains($docs, $contents)) {
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
                        if ($matchLine->test($fileLine)) {
                            $newContents = str_replace($fileLine, "$docs\n$fileLine", $classContents);
                            break;
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
