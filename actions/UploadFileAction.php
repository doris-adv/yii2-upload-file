<?php

namespace sergios\uploadFile\actions;

use yii\base\Action;
use Yii;
use sergios\uploadFile\helpers\UploadHelper;
use sergios\uploadFile\helpers\Path;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;


class UploadFileAction extends Action
{
    private $post = null;

    public function init()
    {
        $this->post = Yii::$app->request->post();
        if(!$this->post){
            throw new NotFoundHttpException();
        }

        parent::init();
    }

    public function run()
    {
        $response = ['success' => false, 'image' => false, 'errors' => false];
        $namespace = $this->post['namespace'];
        $path = $this->post['uploadPath'];
        $maxFileSize = (double)$this->post['maxFileSize'];
        $language = $this->post['language'];
        $tempAttributeName = $this->post['tempAttribute'];
        $resizeOptions = $this->prepareUploadOptions($this->post);

        /** @var $model ActiveRecord */
        $model = Yii::createObject(['class' => $namespace]);
        $image = UploadedFile::getInstance($model, $tempAttributeName);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (\Yii::$app->getRequest()->getIsAjax()) {
            //image resize and save
            $imageName = uniqid() . '.' . $image->getExtension();
            $uploadPath = Path::getUploadPath($path) . $imageName;
            $image->saveAs($uploadPath);

            $maxSizeErrors = UploadHelper::checkOnMaxFileSize($maxFileSize, $image->size, $language);
            if (!empty($maxSizeErrors)) {
                $this->unlinkFile($path,$imageName);
                $response['success'] = false;
                $response['errors'] = $maxSizeErrors;

                return $response;
            }

            if ($resizeOptions['isset']) {//if isset resize options
                $errors = UploadHelper::checkUploadImageSize($uploadPath, $resizeOptions, $language);
                if (!empty($errors)) {
                    $response['success'] = false;
                    $response['errors'] = $errors;

                    return $response;
                }

                UploadHelper::resizeOptional(
                    $path,
                    $imageName,
                    $resizeOptions['resizeWidth'],
                    $resizeOptions['resizeHeight']
                );
            }

            $response['image'] = $imageName;
            $response['success'] = true;
        }

        return $response;
    }

    /**
     * Prepare upload options array
     * @param $post
     * @return array
     */
    private function prepareUploadOptions($post)
    {
        $resizeOptions = ['isset' => false];
        if (ArrayHelper::keyExists('resizeWidth', $post) && ArrayHelper::keyExists('resizeHeight', $post)) {
            $resizeOptions['resizeWidth'] = $post['resizeWidth'];
            $resizeOptions['resizeHeight'] = $post['resizeHeight'];
            $resizeOptions['path'] = $post['uploadPath'];
            $resizeOptions['isset'] = true;
        }

        return $resizeOptions;
    }

    /**
     * Remove file
     * @param $folder
     * @param $name
     */
    private function unlinkFile($folder,$name){
        if(UploadHelper::fileExist($folder,$name)){
            UploadHelper::unlinkFile($folder,$name);
        }
    }
}