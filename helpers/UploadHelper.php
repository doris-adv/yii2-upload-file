<?php

namespace sergios\uploadFile\helpers;

use sergios\uploadFile\components\FileUploader;

use sergios\uploadFile\components\Uploader;
use yii\base\InvalidConfigException;
use yii\imagine\Image;
use Imagine\Image\Box;
use common\helpers\Path;
use Yii;

class UploadHelper
{
    const CONVERT_TYPE_TO_BYTE = 'byte';
    const CONVERT_TYPE_TO_KILOBYTE = 'kilobyte';

    /**
     * Check existing file
     * @param $path
     * @param $attribute
     * @return bool
     */
    public static function fileExist($path, $attribute)
    {
        return is_file(Path::getUploadPath($path) . $attribute);
    }

    /**
     * Unlink file by directory options
     * @param $folder
     * @param $name
     * @return bool
     */
    public static function unlinkFile($folder, $name)
    {
        return unlink(Path::getUploadPath($folder) . $name);
    }

    /**
     * @return string (mine type for images - for upload images in gallery)
     */
    public static function uploadMineTypeForImages()
    {

        return 'image/gif,image/jpeg,image/pjpeg,image/png,image/tiff,image/vnd.microsoft.icon,image/vnd.wap.wbmp';
    }

    /**
     * @param $fileType
     * @return string
     * @throws InvalidConfigException
     */
    public static function uploadMineTypeForFile($fileType){
        switch ($fileType){
            case FileUploader::MINE_TYPE_DOCUMENT:
                return '.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            case FileUploader::MINE_TYPE_EXCEL:
                return '.csv,.xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel';
            case FileUploader::MINE_TYPE_PDF:
                return 'application/pdf';
            default:
                throw new InvalidConfigException("Invalid Mine Type {$fileType}");
        }
    }

    /**
     * Upload Mine type for video file type
     * @return string
     */
    public static function uploadMineTypeForVideo()
    {
        return 'video/*,video/mpeg,video/mp4,video/ogg,video/quicktime,video/webm,video/x-ms-wmv,video/x-flv,video/3gpp,video/3gpp2';
    }


    /**
     * Upload Mine type for audio file
     * @return string
     */
    public static function uploadMineTypeForAudio(){
        return 'audio/*';
    }

    /**
     * Generate errors for uploading file by resize options
     * @param $uploadPath
     * @param $uploadOptions
     * @param $language
     * @return array
     */
    public static function checkUploadImageSize($uploadPath, $uploadOptions,$language)
    {
        $errors = [];
        $imageSize = getimagesize($uploadPath);
        $width = $imageSize[0];
        $height = $imageSize[1];

        if ($width < $uploadOptions['resizeWidth']) {
            $errors[] = Uploader::translateErrors($language)['resize-width-error']."{$uploadOptions['resizeWidth']} px";
            (file_exists($uploadPath)) ? unlink($uploadPath) : '';
        }

        if ($height < $uploadOptions['resizeHeight']) {
            $errors[] = Uploader::translateErrors($language)['resize-height-error']."{$uploadOptions['resizeHeight']} px";
            (file_exists($uploadPath)) ? unlink($uploadPath) : '';
        }

        return $errors;
    }

    /**
     * Generate errors for uploading file by maxFileSize option
     * @param $maxFileSize
     * @param $imageSize
     * @param $language
     * @return array
     */
    public static function checkOnMaxFileSize($maxFileSize,$imageSize,$language){
        $errors = [];
        $availableFileSize = self::convertMaxFileSize($maxFileSize);
        $fileSizeInKilobytes = self::convertMaxFileSize($maxFileSize,self::CONVERT_TYPE_TO_KILOBYTE);

        if($imageSize > $availableFileSize){
            $errors[] = Uploader::translateErrors($language)['max-file-size-error']."<b>{$maxFileSize}  MB, {$fileSizeInKilobytes} KB</b>";
        }

        return $errors;
    }

    /**
     * @param $maxFileSize
     * @param string $convertType
     * @return mixed
     */
    private static function convertMaxFileSize($maxFileSize,$convertType = self::CONVERT_TYPE_TO_BYTE){
        if($convertType == self::CONVERT_TYPE_TO_BYTE){
            return $maxFileSize* 1024 * 1024;
        }

        if($convertType == self::CONVERT_TYPE_TO_KILOBYTE){
            return $maxFileSize* 1024;
        }

    }

    /**
     * Resize image
     * @param $path
     * @param null $heightParam
     * @param null $widthParam
     * @param $name
     * @return bool
     */
    public static function resizeOptional($path, $name, $widthParam = null, $heightParam = null)
    {
        $path = Path::getUploadPath($path);
        $height = $heightParam;
        $width = $widthParam;

        $img = Image::getImagine()->open($path . $name);
        $size = $img->getSize();

        if ($width && $height) {
            Image::thumbnail($path . $name, $width, $height)
                ->save($path . $name, ['quality' => 80]);
            return true;
        }

        if ($width) {
            $ratio = $size->getWidth() / $size->getHeight();
            $height = round($width / $ratio);
        }

        if ($height) {
            $ratio = $size->getHeight() / $size->getWidth();
            $width = round($height / $ratio);
        }

        if (!$width && !$height) {
            return true;
        }

        $box = new Box($width, $height);
        $img->resize($box);
        $img->resize($box)->save($path . $name);

        return true;
    }

    /**
     * @param string $folder
     * @param bool $withDomain
     * @return string
     */
    public static function uploadUrl($folder = '', $withDomain = false)
    {
        $url = '/uploads/' . trim($folder, '/');

        return ($withDomain) ?
            rtrim(Yii::$app->params['domain'], '/') .$url . '/' :
            $url . '/';
    }


    /**
     * This method convert object to array
     * @param $object
     * @return array
     */
    public static function uploaderAdapter($object){
        return (array)$object;
    }

}