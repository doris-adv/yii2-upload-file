<?php
namespace sergios\uploadFile\assets;

use yii\web\AssetBundle;
use yii\web\View;

class UploadFileAsset extends AssetBundle
{
    public $sourcePath = '@vendor/sergios/yii2-upload-file/assets/files';

    public $config = [];
    public $css = [
        'css/upload-file.css'
    ];
    public $js = [
        'js/upload-file.js'
    ];

    public $depends = [
        'FileUploadAssetBS4', // same as dosamigos\fileupload\FileUploadAsset but with Bootstrap4 dependence
        'dosamigos\fileupload\BlueimpLoadImageAsset',
        'dosamigos\fileupload\BlueimpCanvasToBlobAsset',
    ];

    public $jsOptions = ['position' => View::POS_END];
}