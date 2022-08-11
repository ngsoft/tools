<?php ob_start() ?>

declare(strict_types=1);

<?php if(filled($namespace)): ?>
namespace <?= $namespace ?>;

<?php endif; ?>
use NGSOFT\Container\{
    ServiceProvider,
    <?php if(filled($constructor)): ?>SimpleServiceProvider,
    <?php else: ?>NullServiceProvider
    <?php endif; ?>
};

/**
 * <?= $class ?> Facade
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
        <?php if(filled($constructor)): ?>return new SimpleServiceProvider(static::getFacadeAccessor(), \<?= $constructor ?>::class);
        <?php else: ?>return new NullServiceProvider();
        <?php endif; ?>

    }

<?= $methods ?>

}<?php
return ob_get_clean();
