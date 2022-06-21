<?php ob_start() ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

use NGSOFT\Container\{
    ServiceProvider,
    SimpleServiceProvider
};

/**
 * Facade <?= $class ?>

 */
class <?= $class ?> extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return '<?= $accessor ?>';
    }

    protected static function getServiceProvider(): ServiceProvider
    {
        // please change this to declare custom services
        return new SimpleServiceProvider(static::getFacadeAccessor(), new \<?= get_class($instance) ?>);
    }

<?= \NGSOFT\Tools\Utils\FacadeUtils::createMethodsForInstance($instance) ?>

}<?php
return ob_get_clean();
