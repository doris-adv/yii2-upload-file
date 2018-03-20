<?php
/**
 * @var $config array
 * @var $fullUploadUrl string
 */
?>

<div class="<?= "audio-alert{$config['attributes']['attribute']}"?> alert alert-success fade in alert-dismissible hide" style="margin-top:18px;">
    <p class="text-center">
        <b style="font-size: 20px;"><?= $config['translations']['success-alert-text-audio'] ?></b>
        <br />
        <?= $config['translations']['success-bottom-text-audio'] ?>
    </p>
</div>
<audio controls preload = "none"
       class="<?=
       (is_null($config['model']->{$config['attributes']['attribute']})) ? 'hide' : '' ?> <?= 'image-' . $config['attributes']['attribute'] ?>" style="width: 100%;">
    <source src="<?= $fullUploadUrl; ?>" class="<?= 'prev-audio-' . $config['attributes']['attribute'] ?>" type="audio/mpeg">
    Your browser does not support the audio element.
</audio>
