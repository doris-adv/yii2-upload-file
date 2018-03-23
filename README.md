File Uploader Widget

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require sergios/yii2-upload-file 'dev-master'
```

or add

```
"sergios/yii2-upload-file": "dev-master"
```

to the require section of your `composer.json` file.

yii2-upload-file
=========

### Required params
    model - required ( object - \yii\db\ActiveRecord )
    form  - required ( object - \yii\widgets\ActiveForm )
    uploadType - required (allow types are - Uploader::UPLOAD_TYPE_IMAGE | Uploader::UPLOAD_TYPE_VIDEO | Uploader::UPLOAD_TYPE_FILE | Uploader::UPLOAD_TYPE_AUDIO )
    language - required ( allow languages are - en-US | ru-RU | uk-UA )
    attributes - required (array, attribute - needs for saving file name to db and validate (in model rules), tempAttribute needs for transfer uploaded file from asociative array $_FILES, it is public property in your model)
    uploadPath - required (type string)  
### Additional properties
    moduleName - not required, if you use basic version Yii2 you must set muduleName (it is name of you admin module, you may find it in your config file).  
    urlOptions - not required (by default uploadUrl => upload-file, deleteUrl => delete-file), you may set your custom urls and determine them in your controller 
    options => [
        1) multiple - by default false,
        2) uploadMineType - depends on param uploadType, if this option will not be specified upload file widget automaticaly determine uploadMineType.
             You may set another mineTypes using sergios\uploadFile\helpers\UploadHelper. 
             There where some methods fo set mine types: 
              - UploadHelper::uploadMineTypeForImages()
              - UploadHelper::uploadMineTypeForVideo()
              - UploadHelper::uploadMineTypeForAudio()
             Or set your custom mine type in string format
        3) maxFileSize - this property indicated in the format integer or double, by default 3 MB (example 3, 0.2 ....)      
        4) resize - [
            'resizeWidth' => 450,
            'resizeHeight' => 450,
        ]
            Proportional resize by width and height - only for 'uploadType' => Uploader::UPLOAD_TYPE_IMAGE
            If resize option was specified you must identify resizeWidth and resizeHeight, they wiil be required.         
        5) fileMineType => FileUploader::MINE_TYPE_PDF | FileUploader::MINE_TYPE_EXCEL | FileUploader::MINE_TYPE_DOCUMENT
            This property works with uploadType => Uploader::UPLOAD_TYPE_FILE, by default have fileMineType value FileUploader::MINE_TYPE_PDF 
    ]
    templateOptions => [
        1) uploadLimitWindow - show bootstrap alert window with resize information, by default true.
         Depends on resize option, if resize option was not specified uploadLimitWindow param will have false value. 
        2) bootstrapOuterWrapClasses -  by default col-xs-12 col-sm-12 col-md-12 col-lg-12, you may set your custom classes.
        2) bootstrapInnerWrapClasses -  by default col-xs-12 col-sm-12 col-md-12 col-lg-12, you may set your custom classes.
    ] 
> NOTE: You must set in params.php domain name (example 'domain' => 'http://example.domain'), for stable generating path for preview. 
> NOTE: If you using basic version Yii2 you must set moduleName option, for stable generating urls to default processing actions.   

Usage
-----
```
Once the extension is installed, simply use it in your code by  :
```
##Controller
```php
<?php
use yii\helpers\ArrayHelper;
use sergios\uploadFile\actions\UploadFileAction;
use sergios\uploadFile\actions\DeleteFileAction;

class PostController extends \yii\web\Controller
{
   public function actions()
    {
        return ArrayHelper::merge(parent::actions(),[
                'upload-file' => ['class' => UploadFileAction::class], //action for uploading file                   
                'delete-file' => ['class' => DeleteFileAction::class], //action for deleting file
            ]);
        }
}
?>
```
## View
## Upload image example
```php
<?php
use sergios\uploadFile\components\Uploader;
use sergios\uploadFile\UploadFileWidget;
?>

<?=  UploadFileWidget::widget([ 
    'model' => $model,
    'form' => $form,
    'uploadType' => Uploader::UPLOAD_TYPE_IMAGE,
    'language' => 'en-US',
    'uploadPath' => 'image/prev',
    'attributes' => [
        'attribute' => 'image',
        'tempAttribute' => 'tempUploadImage',
    ],
    'options' => [        
        'maxFileSize' => 2,
        'resize' => [
            'resizeWidth' => 450,
            'resizeHeight' => 450,
        ],
    ],
    'templateOptions' => [
        'uploadLimitWindow' => true,
        'bootstrapOuterWrapClasses' => 'col-xs-12 col-sm-12 col-md-6 col-lg-12',
        'bootstrapInnerWrapClasses' => 'col-xs-6 col-sm-6 col-md-6 col-lg-12'
    ]
])?>    
```
## Upload video example
```php
<?php
use sergios\uploadFile\components\Uploader;
use sergios\uploadFile\UploadFileWidget;
?>

<?=  UploadFileWidget::widget([ 
    'model' => $model,
    'form' => $form,
    'uploadType' => Uploader::UPLOAD_TYPE_VIDEO,
    'language' => 'en-US',
    'uploadPath' => 'video/video-folder',
    'attributes' => [
        'attribute' => 'video',
        'tempAttribute' => 'tempUploadVideo',
    ],
    'options' => [       
        'maxFileSize' => 2,
    ],
    'templateOptions' => [      
        'bootstrapOuterWrapClasses' => 'col-xs-12 col-sm-12 col-md-6 col-lg-12',
        'bootstrapInnerWrapClasses' => 'col-xs-6 col-sm-6 col-md-6 col-lg-12'
    ]
])?>     
```
## Upload file example
```php
<?php
use sergios\uploadFile\components\Uploader;
use sergios\uploadFile\UploadFileWidget;
use sergios\uploadFile\components\FileUploader;
?>

<?=  UploadFileWidget::widget([ 
    'model' => $model,
    'form' => $form,
    'uploadType' => Uploader::UPLOAD_TYPE_FILE,
    'language' => 'en-US',
    'uploadPath' => 'files/file-folder',
    'attributes' => [
        'attribute' => 'file',
        'tempAttribute' => 'tempUploadFile',
    ],
    'options' => [
        'fileMineType' => FileUploader::MINE_TYPE_DOCUMENT,//FileUploader::MINE_TYPE_DOCUMENT | FileUploader::MINE_TYPE_EXCEL | FileUploader::MINE_TYPE_PDF
        'maxFileSize' => 25,
    ],
    'templateOptions' => [      
        'bootstrapOuterWrapClasses' => 'col-xs-12 col-sm-12 col-md-6 col-lg-12',
        'bootstrapInnerWrapClasses' => 'col-xs-6 col-sm-6 col-md-6 col-lg-12'
    ]
])?>     
```
## Upload audio file example
```php
<?php
use sergios\uploadFile\components\Uploader;
use sergios\uploadFile\UploadFileWidget;
?>

<?=  UploadFileWidget::widget([ 
    'model' => $model,
    'form' => $form,
    'uploadType' => Uploader::UPLOAD_TYPE_AUDIO,
    'language' => 'en-US',
    'uploadPath' => 'audio/audio-folder',
    'attributes' => [
        'attribute' => 'file',
        'tempAttribute' => 'tempUploadFile',
    ],
    'options' => [        
        'maxFileSize' => 25,
    ],
    'templateOptions' => [      
        'bootstrapOuterWrapClasses' => 'col-xs-12 col-sm-12 col-md-6 col-lg-12',
        'bootstrapInnerWrapClasses' => 'col-xs-6 col-sm-6 col-md-6 col-lg-12'
    ]
])?>     
```


