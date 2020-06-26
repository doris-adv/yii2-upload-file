<?php

namespace doris\uploadFile\helpers;

use Yii;
use yii\helpers\FileHelper;
use yii\base\InvalidCallException;

class Path
{
    const UPLOAD_FOLDER = 'uploads';

    public static function getUploadPath($folder = '')
    {
        $path = self::generateWebRoot();
        if (!empty($folder)) {
            $path .= '/' . trim($folder, '/');
            if (!FileHelper::createDirectory($path)) {
                throw new InvalidCallException("Directory specified in 'path' attribute doesn't exist or cannot be created.");
            }
        }
        return $path . '/';
    }

    /**
     * Get upload url with domain name
     * @param string $folder
     * @param bool $withDomain
     * @return string
     */
    public static function getUploadUrl($folder = '', $withDomain = false)
    {
        $url = '/uploads/' . trim($folder, '/');

        return ($withDomain) ?
            rtrim(Yii::$app->params['domain'], '/') . $url . '/' :
            $url . '/';
    }

    /**
     * Get upload url without domain name
     * @param $folder
     * @param $name
     * @return string
     */
    public static function getUrl($folder, $name)
    {
        return '/uploads/' . trim($folder, '/') . '/' . trim($name, '/');
    }

    /**
     * Generate alias for advanced or basic versions yii2
     * @return mixed
     */
    private static function generateWebRoot()
    {
        $path = $_SERVER['DOCUMENT_ROOT'] . '////' . self::UPLOAD_FOLDER;
        $path = FileHelper::normalizePath($path);

        if (stristr($path, 'backend') !== false) {
            $path = str_replace('backend', 'frontend', $path);
        }

        if (strpos($path, 'web') == false) {
            throw new InvalidCallException("Your DOCUMENT ROOT must contain web ".$path.". Please check your DOCUMENT ROOT settings on your server.");
        }

        return $path;
    }

}