<?php

namespace doris\uploadFile\components;

use yii\base\InvalidConfigException;

class UploaderFactory
{

    /**
     * This method init object by upload type parameter
     * @param $options
     * @return array
     * @throws InvalidConfigException
     */
    static function getConfigByUploadType($options)
    {
        switch ($options['uploadType']) {
            case Uploader::UPLOAD_TYPE_IMAGE:
                $uploader = new ImageUploader($options);
                break;
            case Uploader::UPLOAD_TYPE_FILE:
                $uploader = new FileUploader($options);
                break;
            case Uploader::UPLOAD_TYPE_VIDEO:
                $uploader = new VideoUploader($options);

                break;
            case Uploader::UPLOAD_TYPE_AUDIO:
                $uploader = new AudioUploader($options);
                break;
            default:
                throw new InvalidConfigException('Type must be:image,video,file,audio and it is required param');
        }

        return $uploader->getConfig();
    }
}