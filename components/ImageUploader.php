<?php

namespace sergios\uploadFile\components;

use sergios\uploadFile\helpers\UploadHelper;

class ImageUploader extends Uploader
{
    /**
     * @return $this backend\components\fileUploader\Uploader
     */
    protected function generateConfig()
    {
        $this->options['multiple'] = false;

        return $this;
    }

    protected function getDefaultMineType()
    {
        return UploadHelper::uploadMineTypeForImages();
    }

}