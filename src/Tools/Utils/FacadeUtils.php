<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Utils;

class FacadeUtils
{

    protected static function getFullyQualifiedClassName(string|\Stringable $class)
    {

        $class = (string) $class;

        $split = explode('|', $class);
        foreach ($split as &$class) {
            if (class_exists($class) || interface_exists($class)) {
                $class = NAMESPACE_SEPARATOR . $class;
            }
        }



        return implode('|', $split);
    }

    public static function getClassDocBlocks(object $instance): array
    {

        static $model = " * @method static %s %s(%s)";

        $result = [];
        $reflector = new \ReflectionClass($instance);

        /** @var \ReflectionMethod $rMethod */
        foreach ($reflector->getMethods(\ReflectionMethod::IS_PUBLIC) as $rMethod) {
            if ($rMethod->isStatic() || str_starts_with($rMethod->getName(), '__')) {
                continue;
            }


            $params = [];
            /** @var \ReflectionParameter $rParam */
            foreach ($rMethod->getParameters() as $rParam) {

                $param = sprintf(
                        '%s $%s',
                        self::getFullyQualifiedClassName($rParam->getType() ?? 'mixed'),
                        $rParam->getName()
                );
                if ($rParam->isDefaultValueAvailable()) {
                    $param .= sprintf(' = %s', var_export($rParam->getDefaultValue(), true));
                }

                $params[] = $param;
            }


            $returntype = $rMethod->hasReturnType() ? $rMethod->getReturnType() : 'mixed';

            if (in_array($returntype, ['self', 'static'])) {
                $returntype = get_class($instance);
            }


            $result[] = sprintf(
                    $model,
                    self::getFullyQualifiedClassName($returntype),
                    $rMethod->getName(),
                    implode(', ', $params)
            );
        }
        $result[] = sprintf(' * @see %s', self::getFullyQualifiedClassName(get_class($instance)));

        return $result;
    }

    public static function createDocBlock(string $facade): string
    {


        try {

            if (is_a($facade, \NGSOFT\Facades\Facade::class, true)) {
                $instance = $facade::getFacadeRoot();

                $reflector = new \ReflectionClass($facade);
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
        } catch (\ReflectionException) {

        }

        return '';
    }

}
