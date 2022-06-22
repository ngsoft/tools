<?php ob_start() ?>

    <?= $doc ?>

    public static function <?= $method . $sig ?> : <?= $ret ?>

    {
        <?php if($ret !== 'void'): ?>return <?php endif; ?>static::getFacadeRoot()-><?= $method ?>(<?= implode(', ', $params) ?>);
    }
<?php
return ob_get_clean();