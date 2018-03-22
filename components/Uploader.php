<?php

namespace sergios\uploadFile\components;

use sergios\uploadFile\helpers\UploadHelper;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use Yii;

abstract class Uploader
{
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

    protected $defaultOptions =  [
        'multiple' => false,
        'maxFileSize' => 3,
        'resize' => false,
    ];

    const DEFAULT_TEMPLATE_OPTIONS = [
        'uploadLimitWindow' => true, //default in true (show bootstrap windows with upload options)
        'bootstrapOuterWrapClasses' => 'col-xs-12 col-sm-12 col-md-12 col-lg-12',
        'bootstrapInnerWrapClasses' => 'col-xs-12 col-sm-12 col-md-12 col-lg-12',
    ];

    /**
     * @var $model object
     */
    public $model;

    public $form;

    public $language;

    public $uploadType;

    public $uploadPath;

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
            $this->translations = $this->getTranslations()[$this->language];//get translations
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
                if(!property_exists($this,$key)){
                    throw new InvalidConfigException("Property {$key} are not available");
                }
                $this->{$key} = $options[$key];
            }
        }

        $this->language = (isset($this->language)) ? trim($this->language) : self::DEFAULT_LANGUAGE ;
        $this->urlOptions = (isset($this->urlOptions)) ? $this->urlOptions : self::DEFAULT_URL_OPTIONS;
        $this->defaultOptions['uploadMineType'] = $this->getDefaultMineType();
        $this->options = ArrayHelper::merge($this->defaultOptions,$this->options);
        $this->templateOptions = ArrayHelper::merge(self::DEFAULT_TEMPLATE_OPTIONS,$this->templateOptions);
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
     * This method generate translations for upload template text (for view)
     */
    public function getTranslations()
    {
        return [
            'uk-UA' => [
                'alert-text' => 'Мінімальні вимоги для завантаження ',
                'width' => 'ширина',
                'height' => 'висота',
                'upload-file-text' => 'Завантажити ',
                'after-upload-alert' => 'Потрібно видалити файл щоб завантажити новий!!!',
                'deleting-prompt-text' => 'Ви підтверджуєте видалення файла?',
                'size-label-text' => 'Розмір ',
                'deleting button-text' => 'Видалити файл',
                'file-anchor-text' => 'Переглянути файл',
                'success-alert-text-audio' => 'Аудіо файл успішно завантажено',
                'success-bottom-text-audio' => 'Прослуховувати файл в плеєрі можна після збереження запису!!!!',
                'error-btn-text' => 'Помилка завантаження файла',
            ],
            'ru-RU' => [
                'alert-text' => 'Минимальные требования для загрузки ',
                'width' => 'ширина',
                'height' => 'висота',
                'upload-file-text' => 'Загрузить ',
                'after-upload-alert' => 'Нужно удалить файл чтобы загрузить новый!!!',
                'deleting-prompt-text' => 'Вы подтверждаете удаление файла?',
                'size-label-text' => 'Размер ',
                'deleting button-text' => 'Удалить файл',
                'file-anchor-text' => 'Посмотреть файл',
                'success-alert-text-audio' => 'Аудио файл успешно загружен',
                'success-bottom-text-audio' => 'Прослушивать файл в плеере можно после сохранения записи!!!!',
                'error-btn-text' => 'Ошибка загрузки файла',
            ],
            'en-US' => [
                'alert-text' => 'Minimum requirements for download ',
                'width' => 'width',
                'height' => 'height',
                'upload-file-text' => 'Upload ',
                'after-upload-alert' => 'You need to delete a file to download a new one!!!',
                'deleting-prompt-text' => 'You confirm the deletion of the file? ',
                'size-label-text' => 'Size ',
                'deleting button-text' => 'Delete the file',
                'file-anchor-text' => 'View file',
                'success-alert-text-audio' => 'The audio file has been successfully uploaded',
                'success-bottom-text-audio' => 'Listen to the file in the player after saving the recording!!!!',
                'error-btn-text' => 'Error loading file',
            ]
        ];
    }

    /**
     * This method generate translations for error text (for UploadHelper)
     * @param $language
     * @return mixed
     */
    public static function translateErrors($language){
        $errors = [
            'uk-UA' => [
                'resize-width-error' => 'Ширина завантажуємої фотографії повинна бути рівна або більша ',
                'resize-height-error' => 'Висота завантажуємої фотографії повинна бути рівна або більша ',
                'max-file-size-error' => 'Завантажуємий файл занадто великій, файл не повинен перевищувати '
            ],
            'ru-RU' => [
                'resize-width-error' => 'Ширина загружаемых фотографии должна быть равна или больше ',
                'resize-height-error' => 'Высота загружаемых фотографии должна быть равна или больше ',
                'max-file-size-error' => 'Загружаемых файл слишком большой, файл не должен превышать '
            ],
            'en-US' => [
                'resize-width-error' => 'The width of the uploaded photo should be equal or larger ',
                'resize-height-error' => 'The height of the uploaded photo should be equal or larger ',
                'max-file-size-error' => 'The uploaded file is too large, the file must not exceed '
            ]
        ];

        return ArrayHelper::getValue($errors,$language);
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