<?php
/**
 * @var $config array
 * @var $fullUploadUrl string
 */
?>
<video muted="0" autoplay="autoplay" controls class="<?= 'image-' . $config['attributes']['attribute'] ?>"
       style="<?= ($config['model']->{$config['attributes']['attribute']}) ? "display:block; " : "display:none; " ?>height: 100%;width: 100%;">
    <source src="<?= $fullUploadUrl; ?>">
</video>
