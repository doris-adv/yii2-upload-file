<?php

namespace sergios\uploadFile;

use sergios\uploadFile\assets\UploadFileAsset;
use sergios\uploadFile\helpers\UploadHelper;
use sergios\uploadFile\components\UploaderFactory;
use yii\base\Widget;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\web\View;


class UploadFileWidget extends Widget
{
    /** @var $model ActiveRecord */
    public $model = null; //current model

    /** @var $model ActiveRecord */
    public $form = null;

    public $uploadType;

    public $language = null;

    public $uploadPath = null;

    public $moduleName = null;

    public $urlOptions; // url configuration array

    /** @var $attributes array */
    public $attributes = []; //upload file attributes

    public $options = []; //upload options

    public $templateOptions = []; //template options+

    public function init()
    {
        $view = $this->getView();
        UploadFileAsset::register($view);
    }

    public function run()
    {
        $tempOptions = [];
        foreach (get_object_vars($this) as $optionName => $optionValue) {
            $tempOptions[$optionName] = $optionValue;
        }

        $config = UploaderFactory::getConfigByUploadType($tempOptions);
        $this->registerConfigForJS($config);

        return $this->render('block', ['config' => $config]);
    }


    /**
     * This method register config in global variable for js file
     * @param $config
     */
    private function registerConfigForJS($config)
    {
        $jsConfig = [
            'modelId' => $config['model']->id,
            'attribute' => $config['attributes']['attribute'],
            'tempAttribute' => $config['attributes']['tempAttribute'],
            'attributeId' => Html::getInputId($config['model'], $config['attributes']['attribute']),
            'attributeName' => mb_strtolower($config['model']->getAttributeLabel($config['attributes']['attribute'])),
            'tempAttributeId' => Html::getInputId($config['model'], $config['attributes']['tempAttribute']),
            'uploadType' => $config['uploadType'],
            'uploadUrl' => UploadHelper::uploadUrl($config['uploadPath'], true),
            'translations' => $config['translations'],
            'deleteUrl' => trim($config['urlOptions']['deleteUrl']),
            'uploadPath' => $config['uploadPath'],
            'modelNamespace' => $config['responseJson']['upload-event']['namespace']
        ];

        $this->view->registerJs(' var fileUploadConfig' . $config['attributes']['attribute'] . ' = ' . json_encode($jsConfig) . '',
            View::POS_HEAD);
    }
}