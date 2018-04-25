<?php
use dosamigos\fileupload\FileUpload;
use yii\helpers\Html;
use sergios\uploadFile\helpers\UploadHelper;
use yii\helpers\Url;

/**
 * Template for custom uploading file (for image files)
 * @var $config array
 * @var $config['model'] \yii\db\ActiveRecord
 * @var $this \yii\web\View
 */

/** Set local variables */
$attribute = $config['attributes']['attribute'];
$attributeId = Html::getInputId($config['model'], $attribute);
$attributeName = mb_strtolower($config['model']->getAttributeLabel($config['attributes']['attribute']));//attribute name in lower register
$tempAttributeId = Html::getInputId($config['model'], $config['attributes']['tempAttribute']);
$uploadUrl = UploadHelper::uploadUrl($config['uploadPath'], true);//upload url for file
$fullUploadUrl = (is_null($config['model']->{$attribute})) ? '' : $uploadUrl . $config['model']->{$config['attributes']['attribute']};
/** End set local variables */
?>


<div class="outerWrap<?= $attribute ?> <?= $config['templateOptions']['bootstrapOuterWrapClasses'] ?>" style="margin-top: 20px;">
    <?php if ($config['templateOptions']['uploadLimitWindow'] && !empty($config['options']['resize'])): ?>
        <div class="alert alert-info alert-dismissible upload-info-block" style="display: block;margin-top: 30px;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <p class="text-center"><?= $config['translations']['alert-text'] ?> <?= $attributeName ?>:
                <b><?= $config['translations']['width'] ?>
                    - <?= $config['options']['resize']['resizeWidth'] ?> px, <?= $config['translations']['height'] ?>
                    - <?= $config['options']['resize']['resizeHeight'] ?> px </b>
            </p>
        </div>
    <?php endif; ?>
    <?=
    FileUpload::widget([
        'attribute' => $config['attributes']['tempAttribute'],
        'id' => $tempAttributeId,
        'name' => Html::getInputName($config['model'], $config['attributes']['tempAttribute']),
        'url' => [trim($config['urlOptions']['uploadUrl'])],
        'options' => ['accept' => $config['options']['uploadMineType'], 'multiple' => $config['options']['multiple']],
        'clientOptions' => [
            'maxFileSize' => $config['options']['maxFileSize']
        ],
        'clientEvents' => [
            'fileuploadsubmit' => 'function (e, data) {
                    data.formData = ' . json_encode($config['responseJson']['upload-event']) . ';                   
            }',
            'fileuploadprogress' => 'function (e, data) {
                 uploadStatusButton("' . $tempAttributeId . '","' . $attribute . '");                                         
                 $(document).find(\'.preloader-' . $attribute . '\').show();                
            }',
            'fileuploaddone' => 'function(e, data) {              
                  var errorsBlock' . $attribute . ' = $(document).find(\'.errors-' . $attribute . ' .error-summary\');
                  errorsBlock' . $attribute . '.html(\'\');
                  if(!errorsBlock' . $attribute . '.parent().hasClass(\'hide\')){
                    errorsBlock' . $attribute . '.parent().addClass(\'hide\')
                  }
                   
                  if(data.result.success){
                       if(data.result.image){
                           removeValidationErrors("' . $attribute . '");//remove all validation errors 
                           showPrevByUploadType(data.result.image,"' . $attribute . '");//show preview by upload type file
                           $(document).find(\'#' . $attributeId . '\').attr(\'value\',data.result.image);   
                       } 
                       $(document).find(\'button[type="submit"]\').removeAttr(\'disabled\');
                       $(document).find(\'.preloader-' . $attribute . '\').hide();                        
                       $(document).find(\'.prev-title-' . $attribute . ', .wrapUpload' . $attribute . '\').show();                
                       blockUploadButton("' . $tempAttributeId . '","' . $attribute . '");                                                            
                  }else{
                       $(document).find(\'button[type="submit"]\').removeAttr(\'disabled\');
                       $(document).find(\'.preloader-' . $attribute . '\').hide();
                       errorsBlock' . $attribute . '.parent().removeClass(\'hide\');                           
                       var errors = data.result.errors;                      
                       errorStatusUploadedButton("' . $tempAttributeId . '","' . $config['translations']['error-btn-text'] . '");
                                            
                       for(var i = 0; i <= errors.length; i++){ 
                           if(errors[i]){
                               $(errorsBlock' . $attribute . ').append(\'<br />\'+errors[i]);    
                           }                                                                                 
                       }                             
                  }
                  $(document).find(\'#' . $tempAttributeId . '\').parent().removeClass(\'btn-default\');                    
            }',
        ],
    ]);
    if ($config['model']->{$attribute}) {
        $this->registerJs('
            blockUploadButton("' . $tempAttributeId . '","' . $attribute . '");
    ', $this::POS_READY);
    }
    ?>
    <br />
    <br />
    <div class="errors-<?= $attribute ?> hide">
        <p class="error-summary"></p>
    </div>

    <div class="file-upload-preloader preloader-<?= $attribute ?>" style="display:none;"></div>

    <div style="clear: both;"></div>

    <?= $config['form']->field($config['model'], $attribute)->hiddenInput()->label(false) ?>

    <div class="wrapUpload<?= $attribute ?> row" style="display:<?= ($config['model']->{$attribute}) ? 'block' : 'none' ?>">
        <div class="<?= $config['templateOptions']['bootstrapInnerWrapClasses'] ?>">
            <div class="thumbnail">
                <?php if (!empty($config['options']['resize'])): ?>
                    <span class="prev-title-<?= $attribute ?> label label-info">
                        <?= $config['translations']['size-label-text'] ?>
                        - <?= $config['options']['resize']['resizeWidth'] . '*' . $config['options']['resize']['resizeHeight'] ?>
                    </span>
                <?php endif; ?>
                <h4 class="prev-title-<?= $attribute ?> text-center"
                    style="display:<?= ($config['model']->isNewRecord) ? 'none' : 'block' ?>"><?= $config['model']->getAttributeLabel($attribute) ?></h4>
                <?= $this->render("../templates/_{$config['uploadType']}",['config' => $config,'fullUploadUrl' => $fullUploadUrl]); ?>
            </div>
            <div class="caption">
                <p>
                    <a href="#" class="btn btn-danger" role="button"
                       id="deleteImageButton<?= $attribute ?>"><?= $config['translations']['deleting button-text'] ?></a>
                </p>
            </div>
        </div>
    </div>
</div>
<?php
    $this->registerJs('
        defaultStatusUploadedButton(
            "' . $tempAttributeId . '",
            "' . $config['translations']['upload-file-text'] . ' ' . $attributeName . '"        
        );
        deleteButton("' . $attribute . '");
    ',$this::POS_END);
?>