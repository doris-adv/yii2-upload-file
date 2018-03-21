<?php
use yii\helpers\Html;

/**
 * @var $config array
 * @var $fullUploadUrl string
 */
?>

<?= Html::img(($config['model']->{$config['attributes']['attribute']}) ?
    $fullUploadUrl : '',
    [
        'class' => 'image-' . $config['attributes']['attribute'],
        'style' => ($config['model']->{$config['attributes']['attribute']}) ? "display:block;" : "display:none;"
    ]);
?>