<?php

namespace  sergios\uploadFile\actions;

use yii\base\Action;
use Yii;
use sergios\uploadFile\helpers\UploadHelper;
use yii\db\ActiveRecord;

class DeleteFileAction extends Action
{

    public function run()
    {
        $response = ['success' => true];
        $post = Yii::$app->request->post();
        $id = (integer)$post['id'];
        $fileName = $post['fileName'];
        $attribute = $post['attribute'];
        $path = $post['path'];
        $namespace = $post['namespace'];

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (\Yii::$app->getRequest()->getIsAjax()) {
            Yii::$app->params['deleteAction'] = true;
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