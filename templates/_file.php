<?php
use yii\helpers\Html;

/**
 * @var $config array
 * @var $fullUploadUrl string
 */
?>

<p class="text-center">
    <?= Html::a(
        $config['translations']['file-anchor-text'],
        $fullUploadUrl,
        [
            'style' => 'font-size: 30px;',
            'target' => '_blank',
            'class' => ['fa fa-file image-' . $config['attributes']['attribute'],'prev-link-' . $config['attributes']['attribute']]
        ]);
    ?>
</p>
