<?php

namespace sergios\uploadFile\components;


use sergios\uploadFile\helpers\UploadHelper;

class VideoUploader extends Uploader
{

    protected function generateConfig()
    {
        /** @var $this object backend\components\fileUploader\Uploader */
        $this->options['multiple'] = false;

        return $this;
    }

    protected function getDefaultMineType()
    {
        return UploadHelper::uploadMineTypeForVideo();
    }

    protected function init()
    {
        $this->disabledResize();
    }
}