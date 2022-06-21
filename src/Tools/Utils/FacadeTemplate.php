
declare(strict_types=1);

namespace <?= $namespace ?>;

use NGSOFT\Container\ServiceProvider;


class <?= $class ?> extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return '<?= $accessor ?>';
    }

    protected static function getServiceProvider(): ServiceProvider
    {
        // please change this to declare custom services
        return new NullServiceProvider(static::getFacadeAccessor());
    }

<?= \NGSOFT\Tools\Utils\FacadeUtils::createMethodsForInstance($instance) ?>

}

