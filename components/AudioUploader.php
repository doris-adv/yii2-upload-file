<?php

namespace sergios\uploadFile\components;


use sergios\uploadFile\helpers\UploadHelper;

class AudioUploader extends Uploader
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
        return UploadHelper::uploadMineTypeForAudio();
    }

    protected function init()
    {
        $this->disabledResize();
    }
}