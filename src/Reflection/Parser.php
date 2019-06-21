<?php

namespace NGSOFT\Tools\Reflection;

use Exception;
use Kdyby\ParseUseStatements\UseStatements;
use NGSOFT\Tools\Traits\LoggerWriter;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionClassConstant;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionObject;
use ReflectionProperty;
use Throwable;

class Parser implements LoggerAwareInterface {

    use LoggerAwareTrait;
    use LoggerWriter;

    static public $METHODS_PARSE_ALL = [
        "getProperties",
        "getMethods",
        "getReflectionConstants"
    ];

    /**
     * @var ReflectionClass
     */
    private $reflector;

    /**
     * Creates an instance of logger
     * @param LoggerInterface|null  $logger
     * @return Parser
     */
    public static function create(LoggerInterface $logger = null): Parser {
        return new static($logger);
    }

    /** @param LoggerInterface|null  $logger */
    public function __construct(LoggerInterface $logger = null) {
        $logger && $this->setLogger($logger);
    }

    /**
     * Parse a class
     * @param object|string $class instance or class name
     * @return array<int,Annotation>
     */
    public function parseClass($class): array {
        if (is_string($class) and ( class_exists($class) or interface_exists($class))) {
            $refl = new ReflectionClass($class);
        } elseif (is_object($class)) $refl = new ReflectionObject($class);

        return isset($refl) ? $this->ParseAll($refl) : [];
    }

    /**
     * @param ReflectionClass $classRefl
     * @return array<int,Annotation>
     */
    public function ParseAll(ReflectionClass $classRefl): array {
        $this->reflector = $classRefl;
        $result = [];
        foreach ($this->parseDocComment($classRefl) as $line) {
            list($tag, $value, $desc) = $line;
            $result[] = new Annotation($classRefl, $classRefl, $tag, $value, $desc);
        }
        foreach (static::$METHODS_PARSE_ALL as $method) {
            foreach ($classRefl->{$method}() as $refl) {
                if (
                        ($parsed = $this->parseDocComment($refl))
                        and ! empty($parsed)
                ) {

                    foreach ($parsed as $line) {
                        list($tag, $value, $desc) = $line;
                        $result[] = new Annotation($classRefl, $refl, $tag, $value, $desc);
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Parse Doc Comment
     * @param ReflectionObject|ReflectionClass|ReflectionProperty|ReflectionMethod|ReflectionClassConstant|ReflectionFunction $refl
     * @return array<int,array>
     */
    public function parseDocComment($refl): array {
        $result = [];
        if (method_exists($refl, 'getDocComment')) {

            if ($doc = $refl->getDocComment()) {
                $lines = explode("\n", $doc);
                foreach ($lines as $line) {
                    $line = trim($line);
                    /** don't forget the one liners */
                    $line = trim($line, '/*');
                    $pos = mb_strpos($line, '@');
                    if ($pos !== false) $line = mb_substr($line, $pos);
                    else continue; //not a tag
                    if ($this->isList($line) and ( $res = $this->parseList($line))) {
                        list ($tag, $value) = $res;
                        $result[] = [$tag, $value, null];
                    } elseif ($res = $this->parseLine($line)) {
                        list ($tag, $value, $desc) = $res;
                        $result[] = [$tag, $value, $desc];
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @param string $line
     * @return bool
     */
    private function isList(string $line): bool {

        return !!preg_match('/@.*([\(].*[\)]|\S+\h?+,)/', $line);
    }

    /**
     * Parse a line that contains a flag, a property type or a type
     * @param string $line
     * @return array|null
     */
    private function parseLine(string $line): ?array {
        $tag = null; $val = null; $desc = null;
        //@(?P<tag>\w+)\h?+(?P<value>.*)\r?\n
        //@flag
        //@flag false
        if (preg_match('/@(\w[\w-]+)\h?+(true|1|on)?(false|0|off)?$/i', $line, $matches)) {
            list(, $tag) = $matches;
            $val = count($matches) !== 4;
        }
        //@property type $var Desc
        elseif (preg_match('/@(\w[\w-]+)\h+(\S+)\h+\$(\w+)\h?+(.*)?$/', $line, $matches)) {
            list(, $tag, $type, $var, $desc) = $matches;
            $val = [
                "param" => $var,
                "types" => $type
            ];
        }
        //@var type|type2<key,val>
        elseif (preg_match('/@(\w[\w-]+)\h+(\S+)\h?+(.*)?$/', $line, $matches)) {
            list(, $tag, $types, $desc) = $matches;
            $val = $types;
        }
        //fallback match @tag value that can be all the line
        elseif (preg_match('/@(\w[\w-]+)\h+(.*)/', $line, $matches)) {
            list(, $tag, $val) = $matches;
        }
        if (!is_null($tag) and ! is_null($val)) $result = [$tag, $val, $desc];
        return $result ?? null;
    }

    /**
     * Parse a line that contains a list
     * @param string $line
     * @return array|null
     */
    private function parseList(string $line): ?array {
        $tag = null; $value = null; $arr = [];
        if (preg_match('/@(\w[\w-]+)\h+(\w+)?\h?+\((.*)\)(.*)/', $line, $matches)) {
            list(, $tag, $name, $val) = $matches;
            $hasParams = mb_strpos($val, "=") !== false;

            //hard to create that one!!!
            if ($hasParams) {
                $str = $val;
                do {
                    $pos = mb_strrpos($str, "=");
                    if ($pos > 0) {
                        $v = mb_substr($str, $pos + 1);
                        $v = trim($v);
                        $k = mb_substr($str, 0, $pos);
                        $k = trim($k);
                        $str = $k;
                        $k = preg_replace('/.*?(\w+)$/', '$1', $k);
                        $str = preg_replace(sprintf('/([\h,]+)?%s$/', $k), '', $str);
                        $arr[$k] = $v;
                    }
                } while (mb_substr_count($str, "=") > 0);
            } else $arr = array_map("trim", explode(",", $val));

            if (count($arr) > 0) {
                $val = [];
                foreach ($arr as $k => $v) {
                    try {
                        $tmp = json_decode($v, true);
                        if (json_last_error() !== JSON_ERROR_NONE) throw new Exception();
                        $v = $tmp;
                    } catch (Throwable $ex) {
                        $ex->getCode();
                        if (!preg_match('/^\w+$/', (string) $v)) {
                            $this->log(
                                    sprintf(
                                            "%s Cannot parse annotation %s in %s, json : %s",
                                            __CLASS__, $line, $this->reflector->getFileName(), json_last_error_msg()
                                    )
                            );
                        }
                    }
                    $val[$k] = $v;
                }
            }

            //if list is named
            // eg: @list MyList(arg1, arg2)
            if (mb_strlen($name) > 0) $val = [$name => $val];
            if (!empty($val)) $value = $val;
        }
        if (!is_null($tag) and ! is_null($value)) $result = [$tag, $value];
        return $result ?? null;
    }

    /**
     * Expand Namespace
     * @param string $shortName
     * @return string
     */
    public function expandClassName(ReflectionClass $refl, string $shortName): string {

        return UseStatements::expandClassName($shortName, $refl);
    }

}
