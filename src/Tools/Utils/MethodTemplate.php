<?php ob_start() ?>

    <?= $doc ?>

    public static function <?= $method ?> <?= $sig ?> : <?= $ret ?>

    {
        return static::getFacadeRoot()-><?= $method ?>(<?= implode(', ', $params) ?>);
    }
<?php
return ob_get_clean();