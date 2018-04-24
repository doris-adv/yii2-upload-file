<?php

namespace sergios\uploadFile\components;

use sergios\uploadFile\helpers\UploadHelper;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use Yii;

abstract class Uploader
{
    const FRAMEWORK_ID_BASIC = 'basic';

    /** UPLOAD TYPES */
    const UPLOAD_TYPE_IMAGE = 'image';
    const UPLOAD_TYPE_VIDEO = 'video';
    const UPLOAD_TYPE_FILE = 'file';
    const UPLOAD_TYPE_AUDIO = 'audio';

    /** DEFAULT PARAMETERS */
    const DEFAULT_LANGUAGE = 'uk-UA';

    const DEFAULT_URL_OPTIONS = [
        'uploadUrl' => 'upload-file',
        'deleteUrl' => 'delete-file',
    ];

    protected $defaultOptions = [
        'multiple' => false,
        'maxFileSize' => 3,
        'resize' => false,
    ];

    const DEFAULT_TEMPLATE_OPTIONS = [
        'uploadLimitWindow' => true, //default in true (show bootstrap windows with upload options)
        'bootstrapOuterWrapClasses' => 'col-xs-12 col-sm-12 col-md-12 col-lg-12',
        'bootstrapInnerWrapClasses' => 'col-xs-12 col-sm-12 col-md-12 col-lg-12',
    ];

    const DEFAULT_MODULE_NAME = 'admin';

    /**
     * @var $model object
     */
    public $model;

    public $form;

    public $language;

    public $uploadType;

    public $uploadPath;

    public $moduleName;

    public $urlOptions;

    public $url;

    /**
     * @var $attributes array
     *
     * [
     *   'attribute' => 'image' -  Main attribute
     *   'tempAttribute' => 'tempUploadImage' - Temp attribute (for save in db and for generating errors)
     * ]
     */
    public $attributes;

    /**
     * @var $options array
     */
    public $options;

    /** @var $templateOptions array */
    public $templateOptions;

    /** @var $responseJson array */
    public $responseJson;//return json for ajax action (upload action)

    public $translations;//return translations

    public $uploadMineType;//needs to override in children classes


    final public function __construct($options)
    {
        $this->setParams($options);
        $this->init();
    }

    public function getConfig()
    {
        if (UploaderValidator::validate($this)) {
            $this->translations = $this->getTranslations();//get translations
            $this->prepareResponseJson();
            $this->generateConfig();

            return UploadHelper::uploaderAdapter($this);
        }
    }

    protected function init()
    {
        //bootstrap option initialization in current class

    }

    /**
     * This method set defaults params to properties
     * @param $options
     * @throws InvalidConfigException
     */
    protected function setParams($options)
    {
        if (!empty($options)) {
            foreach ($options as $key => $value) {
                if (!property_exists($this, $key)) {
                    throw new InvalidConfigException("Property {$key} are not available");
                }
                $this->{$key} = $options[$key];
            }
        }

        $this->moduleName = (isset($this->moduleName)) ? trim($this->moduleName) : self::DEFAULT_MODULE_NAME;
        $this->language = (isset($this->language)) ? trim($this->language) : self::DEFAULT_LANGUAGE;
        $this->urlOptions = (isset($this->urlOptions)) ? $this->urlOptions : $this->prepareDefaultUrlOptions();
        $this->defaultOptions['uploadMineType'] = $this->getDefaultMineType();
        $this->options = ArrayHelper::merge($this->defaultOptions, $this->options);
        $this->templateOptions = ArrayHelper::merge(self::DEFAULT_TEMPLATE_OPTIONS, $this->templateOptions);
    }

    /**
     * This method prepare default url option, taking into account version YII2 (basic or advanced)
     * @return array
     */
    protected function prepareDefaultUrlOptions()
    {
        if (Yii::$app->id == self::FRAMEWORK_ID_BASIC) {
            $currentController = Yii::$app->controller->id;
            return [
                'uploadUrl' => "/{$this->moduleName}/{$currentController}/upload-file",
                'deleteUrl' => "/{$this->moduleName}/{$currentController}/delete-file",
            ];
        } else {
            return self::DEFAULT_URL_OPTIONS;
        }
    }

    /**
     * This function prepare json array for ajax events. This parameters will be transfer to upload action.
     */
    protected function prepareResponseJson()
    {
        $this->responseJson['upload-event']['namespace'] = (new \ReflectionClass($this->model))->name;
        $this->responseJson['upload-event']['tempAttribute'] = $this->attributes['tempAttribute'];
        $this->responseJson['upload-event']['uploadPath'] = $this->uploadPath;
        $this->responseJson['upload-event']['maxFileSize'] = $this->options['maxFileSize'];
        $this->responseJson['upload-event']['language'] = $this->language;
        $this->responseJson['upload-event']['_csrf'] = Yii::$app->request->getCsrfToken();
        if (ArrayHelper::isAssociative($this->options['resize'])) {
            $this->responseJson['upload-event'] = ArrayHelper::merge($this->responseJson['upload-event'],
                $this->options['resize']);
        }
    }

    /**
     * This method get translations for upload template text (for view)
     */
    public function getTranslations()
    {
        return require(Yii::getAlias('@vendor/sergios/yii2-upload-file/messages/default/') . "{$this->language}.php");
    }

    /**
     * This method generate translations for error text (for UploadHelper)
     * @param $language
     * @return mixed
     */
    public static function translateErrors($language)
    {
        return require(Yii::getAlias('@vendor/sergios/yii2-upload-file/messages/errors/') . "{$language}.php");
    }

    /**
     * This method disabled resize for video, file and audio file type uploading
     */
    protected function disabledResize()
    {
        if (isset($this->options)) {
            $issetResize = ArrayHelper::keyExists('resize', $this->options);
            if ($issetResize) {
                $this->options['resize'] = false;
            }
        }
    }

    abstract protected function generateConfig();

    abstract protected function getDefaultMineType();

}