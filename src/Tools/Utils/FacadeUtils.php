<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Utils;

use NGSOFT\Facades\Facade,
    ReflectionClass,
    ReflectionException,
    ReflectionMethod,
    ReflectionParameter,
    Stringable;
use const NAMESPACE_SEPARATOR;
use function NGSOFT\Filesystem\require_file,
             str_contains,
             str_starts_with;

class FacadeUtils
{

    protected const KEY_SIG = 0;
    protected const KEY_RET = 1;
    protected const KEY_PARAMS = 2;
    protected const KEY_DOC = 3;

    protected static function isEnumOrConstant(string $input): bool
    {
        return str_contains($input, '::') && defined($input);
    }

    protected static function getFullyQualifiedClassName(string|Stringable|null $class)
    {

        if ($class === null || $class === 'NULL') {
            return 'null';
        }

        $class = (string) $class;

        $splitStr = ['?', '|', '&'];

        foreach ($splitStr as $char) {
            $split = explode($char, $class);

            foreach ($split as &$segment) {
                if (str_starts_with($segment, NAMESPACE_SEPARATOR)) {
                    continue;
                }

                if (empty($segment)) {
                    continue;
                }

                if (class_exists($segment) || interface_exists($segment) || self::isEnumOrConstant($segment)) {
                    $segment = NAMESPACE_SEPARATOR . $segment;
                }
            }
            $class = implode($char, $split);
        }

        return $class;
    }

    protected static function var_exporter(mixed $data): string|null
    {


        if (is_array($data)) {
            $result = '[';
            foreach ($data as $key => $value) {
                $tmp = self::var_exporter($value);
                if ($tmp === null) {
                    return null;
                }
                if (is_int($key)) {
                    $result .= sprintf('%s,', $tmp);
                } else {
                    $result .= sprintf('%s=>%s,', var_export($key, true), $tmp);
                }
            }
            return trim($result, ',') . ']';
        } elseif (is_scalar($data)) {
            return var_export($data, true);
        } elseif (is_null($data)) { return 'null'; }
        return null;
    }

    protected static function getClassMethodsSignatures(object|string $instance): array
    {
        static $model = "(%s)", $sig = self::KEY_SIG, $ret = self::KEY_RET, $prm = self::KEY_PARAMS, $doc = self::KEY_DOC;

        $result = [];

        $class = is_object($instance) ? get_class($instance) : $instance;

        $reflector = new ReflectionClass($instance);

        /** @var ReflectionMethod $rMethod */
        foreach ($reflector->getMethods(ReflectionMethod::IS_PUBLIC) as $rMethod) {
            if ($rMethod->isStatic() || str_starts_with($rMethod->getName(), '__')) {
                continue;
            }

            try {
                /**
                 * Must implements a Spl Interface,
                 * cannot be made static
                 */
                if (($proto = $rMethod->getPrototype()) && $proto->getFileName() === false) {

                    continue;
                }
            } catch (\ReflectionException) {

            }

            $entry = ['', '', [], ''];

            if ($docs = $rMethod->getDocComment()) {
                $entry[$doc] = $docs;
            }

            // the hacky way
            /* $returntype = 'mixed';
              $strMethod = (string) $rMethod;

              if (preg_match('#return \[([^\]]+)#', $strMethod, $matches)) {
              $returntype = trim($matches[1]);
              } */

            $returntype = 'mixed';

            if ($rMethod->hasReturnType()) {
                $returntype = $rMethod->getReturnType();
            } elseif ($rMethod->hasTentativeReturnType()) {
                $returntype = $rMethod->getTentativeReturnType();
            }


            $returntype = (string) $returntype;
            if (in_array($returntype, ['self', 'static'])) {
                $returntype = $class;
            }
            $entry[$ret] = self::getFullyQualifiedClassName($returntype);

            $params = [];
            /** @var ReflectionParameter $rParam */
            foreach ($rMethod->getParameters() as $rParam) {
                $param = sprintf(
                        '%s %s$%s',
                        self::getFullyQualifiedClassName($rParam->getType() ?? 'mixed'),
                        $rParam->canBePassedByValue() ? ($rParam->isVariadic() ? '...' : '') : '&', // so passed by reference
                        $rParam->getName()
                );

                if ($rParam->isDefaultValueAvailable()) {


                    if ($rParam->isDefaultValueConstant()) {
                        $param .= sprintf(' = %s', self::getFullyQualifiedClassName($rParam->getDefaultValueConstantName()));
                    } else { $param .= sprintf(' = %s', self::var_exporter($rParam->getDefaultValue())); }
                }

                $nParam = '$' . $rParam->getName();

                if ($rParam->isVariadic()) {
                    $nParam = "...$nParam";
                }

                $params[$nParam] = $param;
            }


            $entry[$sig] = sprintf($model, implode(', ', $params));
            $entry[$prm] = array_keys($params);

            $result[$rMethod->getName()] = $entry;
        }

        return $result;
    }

    public static function getClassDocBlocks(object|string $instance, bool $static = true): array
    {

        static $model = " * @method %s%s %s%s", $sig = self::KEY_SIG, $ret = self::KEY_RET;

        $static = $static ? 'static ' : '';
        $result = [];
        $class = is_object($instance) ? get_class($instance) : $instance;

        foreach (self::getClassMethodsSignatures($instance) as $method => $entry) {

            $result[] = sprintf($model, $static, $entry[$ret], $method, $entry[$sig]);
        }


        $result[] = sprintf(' * @see %s', self::getFullyQualifiedClassName($class));

        return $result;
    }

    public static function createDocBlock(string $facade): string
    {


        try {

            if (is_a($facade, Facade::class, true)) {
                $instance = $facade::getFacadeRoot();

                $reflector = new ReflectionClass($facade);
                $docs = $reflector->getDocComment() ?: "/**\n */";
                $orig = $docs;

                $result = [];

                $methods = self::getClassDocBlocks($instance);

                $docs = explode("\n", $docs);

                foreach ($docs as $line) {
                    if (str_contains($line, '*/')) {
                        foreach ($methods as $method) {
                            if ( ! str_contains($orig, $method)) {
                                $result[] = $method;
                            }
                        }
                    }
                    $result[] = $line;
                }

                $docs = implode("\n", $result);

                return $docs;
            }
        } catch (ReflectionException) {

        }

        return '';
    }

    public static function createMethodsForInstance(object|string $instance, string $facade = null): string
    {
        static $sig = self::KEY_SIG, $ret = self::KEY_RET, $prm = self::KEY_PARAMS, $doc = self::KEY_DOC;
        $result = [];

        foreach (self::getClassMethodsSignatures($instance) as $method => $entry) {

            if ($facade && method_exists($facade, $method)) {
                continue;
            }


            $code = require_file(__DIR__ . '/Templates/Method.php', [
                'method' => $method,
                'sig' => $entry[$sig],
                'ret' => $entry[$ret],
                'params' => $entry[$prm],
                'doc' => $entry[$doc],
            ]);

            if ( ! blank($code)) {
                $result[] = $code;
            }
        }

        return implode('', $result);
    }

    public static function createMethods(string $facade)
    {
        if (is_a($facade, Facade::class, true)) {
            if ($instance = $facade::getFacadeRoot()) {
                return self::createMethodsForInstance($instance, $facade);
            }
        }
        return '';
    }

    public static function createFacadeCode(object $instance, ?string $name = null, ?string $accessor = null)
    {

        if ( ! $name) {
            $name = class_basename(get_class($instance));
        }

        $namespace = class_namespace($name);
        $class = ucfirst(class_basename($name));
        $provides = $class;

        if (empty($accessor)) {
            $accessor = $class;
        }




        /* if (empty($namespace)) {
          $namespace = 'NGSOFT\\Facades';
          } */


        $code = require_file(__DIR__ . '/Templates/Facade.php', [
            'class' => $class,
            'namespace' => $namespace,
            'accessor' => $accessor,
            'constructor' => get_class($instance),
            //'instance' => $instance,
            'provides' => $provides,
            'methods' => self::createMethodsForInstance($instance),
        ]);

        return "<?php\n{$code}";
    }

}
