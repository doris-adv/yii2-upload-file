<?php
namespace sergios\uploadFile\helpers;

use Yii;
use yii\helpers\FileHelper;
use yii\base\InvalidCallException;

class Path
{
    public static function getUploadPath($folder = '')
    {
        $path = Yii::getAlias('@webroot/web/uploads');
        if (!empty($folder)) {
            $path .= '/' . trim($folder, '/');
            if (!FileHelper::createDirectory(FileHelper::normalizePath($path))) {
                throw new InvalidCallException("Directory specified in 'path' attribute doesn't exist or cannot be created.");
            }
        }
        return $path . '/';
    }

    public static function getUploadUrl($folder = '', $withDomain = false)
    {
        $url = '/uploads/' . trim($folder, '/');

        return ($withDomain) ?
            rtrim(Yii::$app->params['domain'], '/') .$url . '/' :
            $url . '/';
    }

    public static function getUrl($folder, $name)
    {
        return '/uploads/' . trim($folder, '/') . '/' . trim($name, '/');
    }

    public static function getUrlForDefaultImages($folder, $name)
    {
        return '/images/' . trim($folder, '/') . '/' . trim($name, '/');
    }
}