<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\MaskedInput;
use yii\web\JsExpression;
use yii\bootstrap\Modal;
use yii\captcha\Captcha;

use kartik\select2\Select2;

use app\models\Appeal;
use app\models\Tags;
use app\assets\HelperscriptAsset;
use app\assets\JqueryfilerAsset;

/* @var $this yii\web\View */
/* @var $model app\models\Appeal */
/* @var $form yii\widgets\ActiveForm */

HelperscriptAsset::register($this);
JqueryfilerAsset::register($this);

$sMsgTextId = Html::getInputId($model, 'ap_pers_text');
$nMsgTextLen = Appeal::MAX_PERSON_TEXT_LENGTH;


// Показываем количество символов в сообщении
$sJs =  <<<EOT
var oMsgTextField = jQuery("#{$sMsgTextId}"),
    oLenIndicator = jQuery('<div>Осталось символов: </div>').addClass("textmsglength").append('<span />').insertAfter(oMsgTextField),
    showTextLength = function() {
        var sText = oMsgTextField.val(),
            nLen = sText.length;
        if( nLen > {$nMsgTextLen} ) {
            sText = sText.substr(0, {$nMsgTextLen});
            oMsgTextField.val(sText)
            nLen = sText.length;
        }
        oLenIndicator.find('span').text({$nMsgTextLen} - nLen);
    };
showTextLength();
oMsgTextField.on("keyup", function(event){
    showTextLength();
});
EOT;

$this->registerJs($sJs, View::POS_READY, 'appealjs');

$sExt = '["' . implode('","', Yii::$app->params['message.file.ext']) . '"]';
$sFileExt = implode(', ', Yii::$app->params['message.file.ext']);
$nMaxSize = Yii::$app->params['message.file.maxsize'] / 1000000;
$sJs = <<<EOT
$('#appeal-file').filer({
        limit: 1,
        maxSize: {$nMaxSize},
        extensions: {$sExt},
        changeInput: true,
        showThumbs: true,
        appendTo: null,
        theme: "default",
        templates: {
            box: '<ul class="jFiler-item-list"></ul>',
            item: '<li class="jFiler-item">\
                        <div class="jFiler-item-container">\
                            <div class="jFiler-item-inner">\
                                <div class="jFiler-item-thumb">\
                                    <div class="jFiler-item-status"></div>\
                                    <div class="jFiler-item-info">\
                                        <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                    </div>\
                                    {{fi-image}}\
                                </div>\
                                <div class="jFiler-item-assets jFiler-row">\
                                    <ul class="list-inline pull-left">\
                                        <li>{{fi-progressBar}}</li>\
                                    </ul>\
                                    <ul class="list-inline pull-right">\
                                        <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                    </ul>\
                                </div>\
                            </div>\
                        </div>\
                    </li>',
            itemAppend: '<li class="jFiler-item">\
                        <div class="jFiler-item-container">\
                            <div class="jFiler-item-inner">\
                                <div class="jFiler-item-thumb">\
                                    <div class="jFiler-item-status"></div>\
                                    <div class="jFiler-item-info">\
                                        <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                    </div>\
                                    {{fi-image}}\
                                </div>\
                                <div class="jFiler-item-assets jFiler-row">\
                                    <ul class="list-inline pull-left">\
                                        <span class="jFiler-item-others">{{fi-icon}} {{fi-size2}}</span>\
                                    </ul>\
                                    <ul class="list-inline pull-right">\
                                        <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                    </ul>\
                                </div>\
                            </div>\
                        </div>\
                    </li>',
            progressBar: '<div class="bar"></div>',
            itemAppendToEnd: false,
            removeConfirmation: true,
            _selectors: {
                list: '.jFiler-item-list',
                item: '.jFiler-item',
                progressBar: '.bar',
                remove: '.jFiler-item-trash-action',
            }
        },
        dragDrop: {
            dragEnter: null,
            dragLeave: null,
            drop: null,
        },
        addMore: true,
        clipBoardPaste: true,
        excludeName: null,
        beforeShow: function(){return true},
        onSelect: function(){},
        afterShow: function(){},
        onRemove: function(){},
        onEmpty: function(){},
        captions: {
            button: "Выберите файл",
            feedback: "Выбрано файлов для загрузки",
            feedback2: "Выбрано файлов",
            drop: "Перетащите сюда файлы для загрузки",
            removeConfirmation: "Удалить этот файл?",
            errors: {
                filesLimit: "Можно загрузить не более {{fi-limit}} файлов.",
                filesType: "Файлы только типов {$sFileExt} разрешены к загрузке.",
                filesSize: "{{fi-name}} слишком большой! Выберите файл до {{fi-maxSize}} MB.",
                filesSizeAll: "Слишком большие файлы выбрали! Пожалуйста ограничьте их размер {{fi-maxSize}} MB."
            }
        }
    });
EOT;
$this->registerJs($sJs, View::POS_READY, 'jqueryfiler');

$sJs =  <<<EOT
var formatSelect = function(item, text, description) {
    return  item[text] + "<span class=\\"description\\">" + item[description] + "</span>";
}

EOT;
$this->registerJs($sJs, View::POS_END , 'showselectpart');

$aFieldParam = [
    'tags' => [
        'data' => ArrayHelper::map(Tags::getTagslist(Tags::TAGTYPE_TAG), 'tag_id', 'tag_title'),
        'language' => 'ru',
        'options' => [
            'multiple' => true,
//           'tags' => true,
            'placeholder' => 'Выберите теги ...',
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ],
    'tagsstring' => [
        'language' => 'ru',
        'options' => [
            'multiple' => true,
//           'tags' => true,
            'placeholder' => 'Выберите теги ...',
        ],
        'pluginOptions' => [
            'tags' => array_values(ArrayHelper::map(Tags::getTagslist(Tags::TAGTYPE_TAG), 'tag_id', 'tag_title')),
            'allowClear' => true,
        ],
    ],
    'subject' => [
        'data' => ArrayHelper::map(Tags::getTagslist(Tags::TAGTYPE_SUBJECT), 'tag_id', 'tag_title'),
        'language' => 'ru',
//                'disabled' => $isModerate,
//                'readonly' => $isModerate,
        'options' => [
            'placeholder' => 'Выберите тему обращения ...',
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ],
    'subjectfield' => [
        'horizontalCssClasses' => [
            'label' => 'col-sm-1',
            'offset' => 'col-sm-offset-1',
            'wrapper' => 'col-sm-11',
        ],
    ],
    'orgfield' => [
        'horizontalCssClasses' => [
            'label' => 'col-sm-1',
            'offset' => 'col-sm-offset-1',
            'wrapper' => 'col-sm-11',
        ],
        'inputOptions' => [
            'disabled' => true,
        ]
    ],
    'ekisid' => [
        'language' => 'ru',
        'pluginOptions' => [
            'allowClear' => true,
            'initSelection' => new JsExpression('function (element, callback) {
                    if( element.val() > 0 ) {
                        $.ajax({
                            method: "POST",
                            url: "http://hastur.temocenter.ru/task/eo.search/",
                            dataType: "json",
                            data: {
                                filters: {
                                    eo_id: element.val(),
                                },
                                maskarade: {
                                    eo_id: "id",
                                    eo_short_name: "text"
                                },
                                fields: ["eo_id", "eo_short_name", "eo_district_name_id"].join(";")
                            },
                            success: function (data) {
                                callback(data.list.pop());
                            }
                        });
                    }
                }'),
            'ajax' =>[
                'method' => 'POST',
                'url' => "http://hastur.temocenter.ru/task/eo.search/forhost/ask.educom.ru",
                'dataType' => 'json',
                'withCredentials' => true,
                'data' => new JsExpression('function (term, page) {
//                        console.log("data("+term+", "+page+")");
                        return {
                            filters: {eo_name: term, eo_short_name: term},
                            maskarade: {eo_id: "id", eo_short_name: "text", eo_district_name_id: "area_id", eo_subordination_name: "district"},
                            fields: "eo_id;eo_short_name;eo_subordination_name_id;eo_district_name_id",
                            limit: 10,
                            start: (page - 1) * 10,
                            "_": (new Date()).getSeconds()
                        };
                    }'),

                'results' => new JsExpression('function (data, page) {
//                                console.log("results("+page+") data = ", data);
                                var more = (page * 10) < data.total; // whether or not there are more results available
                                return {results: data.list, more: more};
//                                return { results: data.list };
                             }'),
                'id' => new JsExpression(
                    'function(item){return item.id;}'
                ),
            ],
            'formatResult' => new JsExpression(
                'function (item) {
                    return formatSelect(item, "text", "district");
                }'
            ),
            'escapeMarkup' => new JsExpression('function (m) { return m; }'),
        ],

        'pluginEvents' => [
            'change' => 'function(event) {
                    var sIdReg = "'.Html::getInputId($model, 'msg_pers_region').'";
                    jQuery("#'.Html::getInputId($model, 'msg_pers_org').'").val(event.added.text);
                    jQuery("#"+sIdReg).val(event.added.area_id);
//                    console.log("change", event);
//                    console.log("set " + sIdReg + " = " + event.added.area_id);
                }',
        ],

        'options' => [
//                    'multiple' => true,
            'placeholder' => 'Выберите учреждение ...',
        ],
    ],
    'textfield' => [
//            'template' => "{input}\n{hint}\n{error}",
        'horizontalCssClasses' => [
            'label' => 'col-sm-1',
            'offset' => 'col-sm-offset-1',
            'wrapper' => 'col-sm-11',
        ],
    ],
    'filefield' => [
//            'template' => "{input}\n{hint}\n{error}",
        'horizontalCssClasses' => [
            'label' => 'col-sm-1',
            'offset' => 'col-sm-offset-1',
            'wrapper' => 'col-sm-11',
        ],
        'hintOptions' => [
            'class' => 'col-sm-11 col-sm-offset-1',
        ],
    ],
    'file' => [
        'options'=>[
            //                    'accept'=>'image/*',
            'multiple'=> !Yii::$app->user->isGuest
        ],
        'pluginOptions'=>[
            'uploadUrl' => Url::to(['file/upload']),
            'allowedFileExtensions' => Yii::$app->params['message.file.ext'],
            'maxFileCount' => 3,
            'showPreview' => true,
            'showCaption' => true,
            'showRemove' => true,
            'showUpload' => false,
        ]
    ],

    'instrlist' => [
        'name' => 'instructionlist',
        'id' => 'idinstructionlist',
        'language' => 'ru',
        'pluginOptions' => [
            'ajax' =>[
                'method' => 'POST',
                'url' => Url::to(['message/instruction']),
                'dataType' => 'json',
                'withCredentials' => true,
                'data' => new JsExpression('function (term, page) {
                        return {
                            term: term,
                            limit: 10,
                            start: (page - 1) * 10,
                            "_": (new Date()).getSeconds()
                        };
                    }'),

                'results' => new JsExpression('function (data, page) {
                                var more = (page * 10) < data.total; // whether or not there are more results available
                                return {results: data.list, more: more};
                             }'),
                'id' => new JsExpression('function(item){return item.id;}'),
            ],
            'formatResult' => new JsExpression('function (item) { return item.text;}'),
            'escapeMarkup' => new JsExpression('function (m) { return m; }'),
        ],

        'pluginEvents' => [
            'change' => 'function(event) {
                    var sIdReg = "'.Html::getInputId($model, 'msg_empl_command').'",
//                        oInstr = jQuery("#" + sIdReg),
                        oInstrTmp = jQuery("#idinstructionlist");
                    oInstrTmp.val(event.added.text);
//                    oInstr.val(event.added.text);
//                    oInstr.val(oInstr.val() + "\n" + event.added.text);
//                    console.log("change", event);
                }',
        ],

    ],

];

//if( $model->scenario == 'person' ) {
    $sSub = 'person';
    $sLink = Url::to(['subjredirect/html'], true);

    $aFieldParam['subject']['pluginEvents'] = [
        'change' => 'function(event) {
                        var oTempl = jQuery("#modaltemplate"),
                            oMessage = oTempl.clone(),
                            oParent = oTempl.parent();
                        oParent.children(":visible").remove();
                        jQuery.ajax({
                            url: "'.$sLink.'",
                            data: {subjid: event.val},
                            success: function(data, textStatus, jqXHR){
                                oMessage
                                    .append(data)
                                    .attr("id", "doplinks" + event.val)
                                    .appendTo(oParent)
                                    .fadeIn();
                            },
                            error: function( jqXHR, textStatus, errorThrown ) {
//                                console.log(textStatus);
                            },
                            dataType: "html"
                        });
                }',
    ];
//}

?>

<div class="appeal-form">

    <?php $form = ActiveForm::begin([
        'id' => 'appeal-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'validateOnSubmit' => true,
        'validateOnChange' => false,
        'validateOnBlur' => false,
        'validateOnType' => false,
        'layout' => 'horizontal',
        'options'=>[
            'enctype'=>'multipart/form-data'
        ],
        'fieldConfig' => [
//                'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
            'horizontalCssClasses' => [
                'label' => 'col-sm-3',
                'offset' => 'col-sm-offset-3',
                'wrapper' => 'col-sm-9',
//                    'error' => '',
                'hint' => 'col-sm-9 col-sm-offset-3',
            ],
        ],
    ]); ?>

    <div class="col-sm-12">
        <?= $form
            ->field($model, 'ap_subject',$aFieldParam['subjectfield'])
            ->widget(Select2::classname(), $aFieldParam['subject']) ?>
    </div>

        <div class="col-sm-11 col-sm-offset-1">
            <div class="alert alert-warning alert-dismissible" role="alert" id="modaltemplate" style="display: none;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'ap_pers_lastname')->textInput(['maxlength' => 255]) ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'ap_pers_name')->textInput(['maxlength' => 255]) ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'ap_pers_secname')->textInput(['maxlength' => 255]) ?>
    </div>

    <div class="clearfix"></div>

    <div class="col-sm-4">
        <?= $form->field($model, 'ap_pers_email')->textInput(['maxlength' => 255]) ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'ap_pers_phone')->widget(MaskedInput::className(),[
            'name' => 'ap_pers_phone',
            'mask' => '+7(999) 999-99-99'
        ]) ?>
    </div>

    <div class="col-sm-4">
        <?= $form
            ->field($model, 'ekis_id')
            ->widget(Select2::classname(), $aFieldParam['ekisid'])
        . $form
            ->field($model, 'ap_pers_org',['template' => "{input}", 'options' => ['tag' => 'span']])
            ->hiddenInput()
        . $form
            ->field($model, 'ap_pers_region', ['template' => "{input}", 'options' => ['tag' => 'span']])
            ->hiddenInput()
        ?>
    </div>


    <div class="clearfix"></div>

    <div class="col-sm-12">
        <?= $form
            ->field($model, 'ap_pers_text', $aFieldParam['textfield'])
            ->textarea(['rows' => 6]) ?>
    </div>

    <?php
    if( $model->isNewRecord ):
        ?>
        <div class="col-sm-12">
            <?= $form
                ->field($model, 'file[]', $aFieldParam['filefield'])
                ->fileInput(['multiple' => true])
                ->hint('Максимальный размер файла: '
                    . sprintf("%.1f Mb", Yii::$app->params['message.file.maxsize'] / 1000000)
                    . ', Допустимые типы файлов: '
                    . implode(',', Yii::$app->params['message.file.ext'])
                )
            ?>

        </div>

        <?php /*
        if( $model->isUseCaptcha() ) {
            ?>
            <div class="col-sm-12">
                <?= $form->field($model, 'verifyCode', $aFieldParam['filefield'])->widget(Captcha::className(), [
                    'captchaAction' => 'message/captcha',
                    'template' => '<div class="row"><div class="col-lg-2">{image}</div><div class="col-lg-3">{input}</div><div class="clearfix"></div><div class="col-lg-5">Введите код с картинки в текстовое поле</div></div>',
                ]) ?>
            </div>
        <?php
        } */
        ?>

    <?php
    else:
/*        $aFiles = $model->getUserFiles(true);
        if( count($aFiles) > 0 ):
            ?>
            <div class="col-sm-12">
                <label for="message-msg_pers_text" class="control-label col-sm-1">Файлы</label>
                <div class="col-sm-11">
                    <?php
                    foreach($aFiles As $oFile):
                        /** @var File  $oFile */ /*
                        ?>
                        <div class="btn btn-default">
                            <?= Html::a( Html::encode($oFile->file_orig_name), $oFile->getUrl()) ?>
                            <?= Html::a('<span class="glyphicon glyphicon-remove"></span>', ['file/delete', 'id' => $oFile->file_id], ['class'=>"link_with_confirm", 'title'=>'Удалить файл ' . Html::encode($oFile->file_orig_name)]) ?>
                        </div>
                    <?php
                        //                    <!-- ?= Html::a('<span class="glyphicon glyphicon-remove"></span>', ['file/delete', 'id' => $oFile->file_id]) ? -->
                    endforeach;
                    ?>
                    <div class="clearfix"></div>
                </div>
            </div>
        <?php
        endif; */
    ?>
    <?php
    endif;
    ?>
    <div class="clearfix"></div>

    <?php /*= $form->field($model, 'ap_created')->textInput() ?>

    <?= $form->field($model, 'ap_next_act_date')->textInput() ?>

    <?= $form->field($model, 'ap_pers_name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'ap_pers_secname')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'ap_pers_lastname')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'ap_pers_email')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'ap_pers_phone')->textInput(['maxlength' => 24]) ?>

    <?= $form->field($model, 'ap_pers_org')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'ap_pers_region')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'ap_pers_text')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'ap_empl_command')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'ap_comment')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'ap_subject')->textInput() ?>

    <?= $form->field($model, 'ap_empl_id')->textInput() ?>

    <?= $form->field($model, 'ap_curator_id')->textInput() ?>

    <?= $form->field($model, 'ekis_id')->textInput() ?>

    <?= $form->field($model, 'ap_state')->textInput() ?>

    <?= $form->field($model, 'ap_ans_state')->textInput() */ ?>

    <div class="col-sm-12">
        <div class="form-group" style="margin-top: 2em;">
            <label for="message-msg_pers_text" class="control-label col-sm-1">&nbsp;</label>
            <div class="col-sm-3">
                <?= Html::submitButton($model->isNewRecord ? 'Отправить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success btn-block' : 'btn btn-primary btn-block']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
