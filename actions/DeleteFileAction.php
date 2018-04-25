<?php

namespace sergios\uploadFile\actions;

use yii\base\Action;
use Yii;
use sergios\uploadFile\helpers\UploadHelper;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

class DeleteFileAction extends Action
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
        $response = ['success' => true];
        $id = (integer)$this->post['id'];
        $fileName = $this->post['fileName'];
        $attribute = $this->post['attribute'];
        $path = $this->post['path'];
        $namespace = $this->post['namespace'];

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (\Yii::$app->getRequest()->getIsAjax()) {
            if (UploadHelper::fileExist($path, $fileName)) {
                $response['success'] = UploadHelper::unlinkFile($path, $fileName);
            }
            //if not new record
            if ($id != 0) {
                /** @var $model ActiveRecord */
                $model = Yii::createObject(['class' => $namespace]);
                $record = $model::findOne(['id' => $id]);
                $record->{$attribute} = null;
                $record->save();
            }
        }

        return $response;
    }
}