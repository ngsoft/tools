<?php

declare(strict_types=1);

namespace NGSOFT\Enums;

use InvalidArgumentException;

/**
 *
 */
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
            $classLn = -1;

            if ($classContents = file_get_contents($fName)) {


                if ($orig === false) {
                    $tokens = token_get_all($classContents, TOKEN_PARSE);

                    $checkLines = [];

                    foreach ($tokens as $token) {
                        if (is_array($token)) {
                            list($tokenIndex, $chars, $lineNumber) = $token;
                            $tokenName = token_name($tokenIndex);
                            if ($tokenName !== 'T_EXTENDS') continue;
                            // line number begins to 1 and array to 0
                            $checkLines[] = $lineNumber - 1;
                        }
                    }
                    if (count($checkLines) > 0) {
                        $lines = explode("\n", $classContents);
                        foreach ($checkLines as $lineNumber) {
                            $lineContents = $lines[$lineNumber];
                            if (str_contains($lineContents, $relClassName) && str_contains($lineContents, 'Enum')) {
                                $newContents = str_replace($lineContents, "$docs\n$lineContents", $classContents);
                            }
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
