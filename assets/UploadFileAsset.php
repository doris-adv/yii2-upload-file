<?php
namespace sergios\uploadFile\assets;

use yii\web\AssetBundle;
use yii\web\View;

class UploadFileAsset extends AssetBundle
{
    public $sourcePath = '@vendor/uploadFile/assets/files';

    public $config = [];


    public $css = [];
    public $js = [
        'js/upload-file.js'
    ];

    public $depends = [
        'dosamigos\fileupload\FileUploadAsset',
        'dosamigos\fileupload\BlueimpLoadImageAsset',
        'dosamigos\fileupload\BlueimpCanvasToBlobAsset',
    ];

    public $jsOptions = ['position' => View::POS_END];
}