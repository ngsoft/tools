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

    protected static function getFullyQualifiedClassName(string|Stringable $class)
    {

        $class = (string) $class;

        $splitStr = ['|', '&'];

        foreach ($splitStr as $char) {
            $split = explode($char, $class);

            foreach ($split as &$segment) {
                if (str_starts_with($segment, NAMESPACE_SEPARATOR)) {
                    continue;
                }

                if (class_exists($segment) || interface_exists($segment)) {
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
                if ($tmp === null) { return null; }
                if (is_int($key)) {
                    $result .= sprintf('%s,', $tmp);
                } else { $result .= sprintf('%s=>%s,', var_export($key, true), $tmp); }
            }
            return trim($result, ',') . ']';
        } elseif (is_scalar($data) || is_null($data)) {
            return strtolower(var_export($data, true));
        }
        return null;
    }

    protected static function getClassMethodsSignatures(object $instance): array
    {
        static $model = "(%s)", $sig = self::KEY_SIG, $ret = self::KEY_RET, $prm = self::KEY_PARAMS, $doc = self::KEY_DOC;

        $result = [];

        $reflector = new ReflectionClass($instance);

        /** @var ReflectionMethod $rMethod */
        foreach ($reflector->getMethods(ReflectionMethod::IS_PUBLIC) as $rMethod) {
            if ($rMethod->isStatic() || str_starts_with($rMethod->getName(), '__')) {
                continue;
            }


            /**
             * Must implements a Spl Interface,
             * cannot be made static
             */
            if (($proto = $rMethod->getPrototype()) && empty(class_namespace($proto->class))) {
                continue;
            }


            $entry = ['', '', [], ''];

            if ($docs = $rMethod->getDocComment()) {
                $entry[$doc] = $docs;
            }


            $returntype = $rMethod->hasReturnType() ? $rMethod->getReturnType() : 'mixed';
            $returntype = (string) $returntype;
            if (in_array($returntype, ['self', 'static'])) {
                $returntype = get_class($instance);
            }
            $entry[$ret] = self::getFullyQualifiedClassName($returntype);

            $params = [];
            /** @var ReflectionParameter $rParam */
            foreach ($rMethod->getParameters() as $rParam) {
                $param = sprintf(
                        '%s %s$%s',
                        self::getFullyQualifiedClassName($rParam->getType() ?? 'mixed'),
                        $rParam->canBePassedByValue() ? '' : '&', // so passed by reference
                        $rParam->getName()
                );
                if ($rParam->isDefaultValueAvailable()) {
                    $default = $rParam->getDefaultValue();
                    $param .= sprintf(' = %s', self::var_exporter($default));
                }

                $params['$' . $rParam->getName()] = $param;
            }


            $entry[$sig] = sprintf($model, implode(', ', $params));
            $entry[$prm] = array_keys($params);

            $result[$rMethod->getName()] = $entry;
        }

        return $result;
    }

    public static function getClassDocBlocks(object $instance): array
    {

        static $model = " * @method static %s %s%s", $sig = self::KEY_SIG, $ret = self::KEY_RET;

        $result = [];

        foreach (self::getClassMethodsSignatures($instance) as $method => $entry) {

            $result[] = sprintf($model, $method, $entry[$ret], $entry[$sig]);
        }


        $result[] = sprintf(' * @see %s', self::getFullyQualifiedClassName(get_class($instance)));

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

    public static function createMethodsForInstance(object $instance, string $facade = null): string
    {
        static $sig = self::KEY_SIG, $ret = self::KEY_RET, $prm = self::KEY_PARAMS, $doc = self::KEY_DOC;
        $result = [];

        foreach (self::getClassMethodsSignatures($instance) as $method => $entry) {

            if ($facade && method_exists($facade, $method)) {
                continue;
            }


            $code = require_file(__DIR__ . '/MethodTemplate.php', [
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

    public static function createFacadeCode(string $name, object $instance, ?string $accessor = null)
    {

        $namespace = class_namespace($name);
        $class = ucfirst(class_basename($class));

        if (empty($accessor)) {
            $accessor = $class;
        }

        if (empty($namespace)) {
            $namespace = 'NGSOFT\\Facades';
        }

                $code = require_file(__DIR__ . '/FacadeTemplate.php', [
                    'class' => $class,
                    'namespace' => $namespace,
                    'accessor'=> $accessor,
                    'instance' => $instance
                ]);




    }

}
