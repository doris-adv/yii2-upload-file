<?php

namespace sergios\uploadFile\components;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;

class UploaderValidator
{
    /**
     * This Method validate params
     * @param Uploader $uploader
     * @return bool
     */
    static function validate(Uploader $uploader)
    {
        self::validateRequiredAttributes($uploader);
        self::validateAnotherAttributes($uploader);

        return true;
    }

    /**
     * This method validate another attributes and set default values in they have not them
     * @param Uploader $uploader
     */
    private static function validateAnotherAttributes(Uploader $uploader)
    {
        self::findAdminModule($uploader);
        self::validateAttributesParams($uploader);
        self::prepareUrlOptions($uploader);
        self::prepareOptions($uploader);
    }

    /**
     * This method check if exist admin module in basic YII2 aplication
     * @param Uploader $uploader
     * @throws InvalidConfigException
     */
    private static function findAdminModule(Uploader $uploader){
        if(Yii::$app->id == $uploader::FRAMEWORK_ID_BASIC){
            if(!self::keyExist($uploader->moduleName,Yii::$app->modules)){
                throw new InvalidConfigException("Module with name {$uploader->moduleName}, does not exist.");
            }
        }
    }

    /**
     * This method validate url options
     * @param Uploader $uploader
     * @throws InvalidConfigException
     */
    private static function prepareUrlOptions(Uploader $uploader)
    {
        /** Check on errors params in urlOptions config */
        if (!self::keyExist('uploadUrl', $uploader->urlOptions)) {
            throw new InvalidConfigException('uploadUrl - required param in urlOptions config');
        }
        if (!self::keyExist('deleteUrl', $uploader->urlOptions)) {
            throw new InvalidConfigException('deleteUrl - required param in urlOptions config');
        }
    }

    /**
     * This method validate resize options
     * @param Uploader $uploader
     * @throws InvalidConfigException
     */
    private static function prepareOptions(Uploader $uploader)
    {
        /** Check attributes for resize options */
        if ($uploader->options['resize'] != false) {
            /** Check resize options empty value */
            if (!self::keyExist('resizeWidth', $uploader->options['resize'])) {
                throw new InvalidConfigException('Option resizeWidth must be set, it required param');
            }
            if (!self::keyExist('resizeHeight', $uploader->options['resize'])) {
                throw new InvalidConfigException('Option resizeHeight must be set, it required param');
            }
        }
        /** End checking attributes for resize options */
    }

    /**
     * This method validate attributes param
     * @param $uploader
     * @throws InvalidConfigException
     */
    public static function validateAttributesParams(Uploader $uploader)
    {
        if (!self::keyExist('attribute', $uploader->attributes)) {
            throw new InvalidConfigException('attribute must be set, it required param');
        }

        if (!self::keyExist('tempAttribute', $uploader->attributes)) {
            throw new InvalidConfigException('tempAttribute must be set, it required param');
        }

        if (self::getType($uploader->attributes['attribute']) != 'string') {
            throw new InvalidConfigException('attribute param must be in string format');
        }

        if (self::getType($uploader->attributes['tempAttribute']) != 'string') {
            throw new InvalidConfigException('tempAttribute param must be in string format');
        }
    }

    /**
     * This method validate required params
     * @param Uploader $uploader
     * @throws InvalidConfigException
     */
    private static function validateRequiredAttributes(Uploader $uploader)
    {
        if (empty($uploader->model) || self::getType($uploader->model) != 'object') {
            throw new InvalidConfigException('Model - is required param and it must be object');
        }

        if (isset($uploader->language) && self::getType($uploader->language) != 'string') {
            throw new InvalidConfigException('language param must be in string format.');
        }

        if (isset($language) && !ArrayHelper::keyExists(trim($language), $uploader->getTranslations())) {
            $languages = array_keys($uploader->getTranslations());
            $languageStr = '';
            foreach ($languages as $key => $language) {
                $languageStr .= ' ' . $language;
            }

            throw new InvalidConfigException('Permissible languages -' . $languageStr);
        }

        if (empty($uploader->uploadPath) || self::getType($uploader->uploadPath) != 'string') {
            throw new InvalidConfigException('uploadPath - is required param and it must be in string format');
        }

        if (empty($uploader->form) || (new \ReflectionClass($uploader->form))->getShortName() != 'ActiveForm') {
            throw new InvalidConfigException('form - is required param and it must be inherit from ActiveForm class');
        }

        if (empty($uploader->attributes)) {
            throw new InvalidConfigException('attributes - must be set, it required param');
        }

        if (!ArrayHelper::keyExists(trim($uploader->language), $uploader->getTranslations())) {
            $languages = array_keys($uploader->getTranslations());
            $languageStr = '';
            foreach ($languages as $key => $language) {
                $languageStr .= ' ' . $language;
            }

            throw new InvalidConfigException('Permissible languages -' . $languageStr);
        }

    }

    /**
     * Check if key exist in array options
     * @param $key
     * @param $value
     * @return bool
     */
    private static function keyExist($key, $value)
    {
        return ArrayHelper::keyExists($key, $value);
    }

    /**
     * Get type of param
     * @param $attribute
     * @return string
     */
    public static function getType($attribute)
    {
        return gettype($attribute);
    }
}
