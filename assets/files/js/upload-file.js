
function showPrevByUploadType(fileName,attribute){
    var config = getConfig(attribute);
    var imagePrev = $(document).find('.image-'+attribute);
    var previewLink = $(document).find(".prev-link-"+attribute);
    var previewAudio = $(document).find(".prev-audio-"+attribute);

    if(typeof(previewLink) !== 'undefined' && config.uploadType === 'file'){
        previewLink.removeAttr('href').attr('href',config.uploadUrl+fileName);
    }

    if(typeof(previewAudio) !== 'undefined' && config.uploadType === 'audio'){
        previewAudio.removeAttr('src').attr('src',config.uploadUrl+fileName);
        $(document).find('.audio-alert'+attribute).removeClass('hide').addClass('show');
        $(document).find('.image-'+attribute).addClass('hide');
    }

    if(config.uploadType === 'image' || config.uploadType === 'video'){
        imagePrev.removeAttr('src').attr('src',config.uploadUrl+fileName);
    }

    imagePrev.show();
}

function uploadStatusButton(inputId,hiddenInputId){
    var uploadButton = $(document).find('#'+inputId).parent();
    uploadButton.find("span").text("Зачекайте йде завантаження файла");
    $(document).find('input[name="image"]').attr('disabled',true);
    $(document).find('button[type="submit"]').attr("disabled", true);
}

function defaultStatusUploadedButton(inputId,text){
    var uploadButton = $(document).find('#'+inputId).parent();
    if(uploadButton.hasClass('btn-danger')){
        uploadButton.removeClass('btn-danger').addClass('btn-success');
    }
    uploadButton.find("span").text(text);
    $(document).find("#"+inputId).removeAttr('disabled');
}

function errorStatusUploadedButton(inputId,text){

    var uploadButton = $(document).find('#'+inputId).parent();
    uploadButton.find("span").text(text);
    uploadButton.removeClass('btn-success').addClass('btn-danger');
}

function blockUploadButton(inputId,attribute){
    var config = getConfig(attribute);
    var uploadButton = $(document).find('#'+inputId).parent();

    uploadButton.find("input").attr("disabled",true);
    uploadButton.addClass('btn btn-warning');
    uploadButton.find("span").text(config.translations['after-upload-alert']);
}

function unlockUploadButton(inputId){
    $(document).find('#'+inputId).parent().removeClass('btn btn-warning').addClass('btn btn-success');
}

function removeValidationErrors(attribute){
    var config = getConfig(attribute);
    var attributeInputWrap = $(document).find('#'+config.attributeId).parent();

    if(attributeInputWrap.hasClass("has-error")){
        attributeInputWrap.find(".help-block").html("");
    }
}

function deleteButton(attribute){
    var config = getConfig(attribute);
    var deleteButton = $(document).find('#deleteImageButton'+config.attribute);
    deleteButton.on('click',function(){
        if (confirm(config.translations['deleting-prompt-text'])) {
            var image = $(document).find('#'+config.attributeId).val();
            $.ajax({
                url: config.deleteUrl,
                data: {
                    id: config.modelId,
                    namespace: config.modelNamespace,
                    path: config.uploadPath,
                    image: image
                },
                type: "post",
                success: function (response) {
                    if(response.success){
                        $(document).find('#'+config.attributeId).removeAttr('value');
                        defaultStatusUploadedButton(
                            config.tempAttributeId,
                            config.translations['upload-file-text']+" "+config.attribute
                        );
                        unlockUploadButton(config.tempAttributeId);
                        $(document).find('.wrapUpload'+config.attribute).hide();
                    }
                },
                dataType: "json"
            });
        }else{
            return true;
        }
    });
}

//get current config from registered in head variables
function getConfig(attribute){
    return  eval('fileUploadConfig'+attribute);
}