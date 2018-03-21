<?php

namespace sergios\uploadFile\components;

use sergios\uploadFile\helpers\UploadHelper;

class FileUploader extends Uploader
{
    const MINE_TYPE_PDF = 'pdf';
    const MINE_TYPE_EXCEL = 'excel';
    const MINE_TYPE_DOCUMENT = 'document';

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
        $fileMineType = isset($this->options['fileMineType']) ? $this->options['fileMineType'] : self::MINE_TYPE_PDF;

        return UploadHelper::uploadMineTypeForFile($fileMineType);
    }

    protected function init()
    {
        $this->disabledResize();
    }
}