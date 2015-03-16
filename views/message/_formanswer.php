<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use vova07\imperavi\Widget;

use app\assets\ListdataAsset;
use app\models\Msgflags;
use app\models\File;
// use app\assets\FileapiAsset;
use app\assets\JqueryfilerAsset;

use kartik\file\FileInput;


/* @var $this yii\web\View */
/* @var $model app\models\Message */
/* @var $form yii\widgets\ActiveForm */

ListdataAsset::register($this);
// FileapiAsset::register($this);
JqueryfilerAsset::register($this);

$aOp = array_reduce(
    Msgflags::getStateTrans($model->msg_flag),
    function ( $carry , $item ) {
        $sTitle = Msgflags::getStateTitle($item, 'fl_command');
        if( $sTitle != '' ) {
            $carry[$item] = $sTitle;
        }
        return $carry;
    },
    []
);

?>

<div class="message-form">
    <?php $form = ActiveForm::begin([
            'id' => 'message-form',
            'layout' => 'horizontal',
            'options'=>[
                'enctype'=>'multipart/form-data'
            ],
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'label' => 'col-sm-2',
                    'offset' => 'col-sm-offset-2',
                    'wrapper' => 'col-sm-10',
                ],
            ],
    ]);

    ?>


    <?php
    if( strlen($model->msg_empl_command) > 0 ):
        ?>
        <div class="alert alert-warning" role="alert"><strong>Поручение:</strong> <?= Html::encode($model->msg_empl_command) ?></div>
    <?php
    endif;
    ?>

    <?php
    if( strlen($model->msg_comment) > 0 ):
        ?>
        <div class="alert alert-warning" role="alert"><strong>Комментарий:</strong> <?= Html::encode($model->msg_comment) ?></div>
    <?php
    endif;
    ?>

    <?php
    if( strlen($model->msg_empl_remark) > 0 ):
        ?>
        <div class="alert alert-danger" role="alert"><strong>Замечание:</strong> <?= Html::encode($model->msg_empl_remark) ?></div>
    <?php
    endif;
    ?>

    <?php /*
 = $form->field(
            $model,
            'msg_answer')
        ->textarea(['rows' => 6]) */ ?>
    <?= $form->field(
            $model,
            'msg_answer')
        ->widget(Widget::className(), [
            'settings' => [
                'lang' => 'ru',
                'minHeight' => 200,
                'buttons' => ['formatting', 'bold', 'italic', 'deleted', 'unorderedlist', 'orderedlist', 'link', 'alignment'], // 'outdent', 'indent', 'image',
                'plugins' => [
//                    'clips',
                    'fullscreen',
                ]
            ]
            ]) ?>
    <?= $form->field($model, 'msg_flag', ['template' => "{input}", 'options' => ['tag' => 'span']])->hiddenInput();  ?>
    <?php
    $aFiles = $model->getUserFiles(false);
    $nFilesExists = count($aFiles);
    if( $nFilesExists > 0 ):
        ?>
        <div class="form-group">
            <label for="message-msg_pers_text" class="control-label col-sm-2">Файлы</label>
            <div class="col-sm-10">
                <?php
                foreach($aFiles As $oFile):
                    /** @var File  $oFile */
                    ?>
                    <div class="btn btn-default">
                        <?= Html::a( Html::encode($oFile->file_orig_name), $oFile->getUrl()) ?>
                        <!-- ?= Html::a('<span class="glyphicon glyphicon-remove"></span>', ['file/delete', 'id' => $oFile->file_id]) ? -->
                    </div>
                <?php
                endforeach;
                ?>
                <div class="clearfix"></div>
            </div>
        </div>
    <?php
    endif; // if( $model->countAvalableFile() > 0 ):
    $nFiles = Yii::$app->params['message.file.answercount'] - $nFilesExists;
    if( $nFiles > 0 ):
        $sPreview = '<div class="file-preview-frame" id="{previewId}" data-fileindex="{fileindex}" title="{caption}" style="height: auto;">{footer}</div>';
        $aFieldParam = [
            'filefield' => [
        //            'template' => "{input}\n{hint}\n{error}",
                'horizontalCssClasses' => [
                    'label' => 'col-sm-2',
                    'offset' => 'col-sm-offset-2',
                    'wrapper' => 'col-sm-10',
                ],
                'hintOptions' => [
                    'class' => 'col-sm-10 col-sm-offset-2',
                ],
            ],
            'file' => [
                'options'=>[
                    'multiple' => ($nFiles > 1)
                ],
                'pluginOptions'=>[
                    'uploadUrl' => Url::to(['file/upload']),
                    'allowedFileExtensions' => Yii::$app->params['message.file.ext'],
                    'maxFileCount' => $nFiles,
                    'showPreview' => true,
                    'showCaption' => true,
                    'showRemove' => true,
                    'showUpload' => false,
//                    'previewFileType' => 'image',

                    'layoutTemplates' => [
                        'preview' => '<div class="file-preview {class}">' .
                            ' <div class="close fileinput-remove">×</div>' .
                            ' <div class="{dropClass}">' .
                            ' <div class="file-preview-thumbnails">' .
                            ' </div>' .
                            ' <div class="clearfix"></div>' .
                            ' <div class="file-preview-status text-center text-success"></div>' .
                            ' <div class="kv-fileinput-error"></div>' .
                            ' </div>' .
                            '</div>',
                        'actionUpload' => '',
                    ],
                    'previewTemplates' => [
                        'object' => $sPreview,
                        'audio' => $sPreview,
                        'video' => $sPreview,
                        'image' => $sPreview,
                        'text' => $sPreview,
                        'flash' => $sPreview,
                        'html' => $sPreview,
                        'other' => $sPreview,
                    ],
                    'previewSettings' => [],
                    'fileActionSettings' => [
                        'indicatorNew' => '',
                    ]

                ]
            ]
        ];
/*
                        'preview' => '<div class="file-preview {class}">\\n' +
                            ' <div class="close fileinput-remove">×</div>\\n' +
                            ' <div class="{dropClass}">\\n' +
                            ' <div class="file-preview-thumbnails">\\n' +
                            ' </div>\\n' +
                            ' <div class="clearfix"></div>' +
                            ' <div class="file-preview-status text-center text-success"></div>\\n' +
                            ' <div class="kv-fileinput-error"></div>\\n' +
                            ' </div>\\n' +
                            '</div>',

*/
/*

http://rubaxa.github.io/jquery.fileapi/ - тут смотрим примеры и html
http://mailru.github.io/FileAPI/ - тут API

*/
/*
$sJs = <<<'EOT'
$('#multiupload').fileapi({
   url: 'http://rubaxa.org/FileAPI/server/ctrl.php',
   multiple: true,
   elements: {
        ctrl: { upload: '.js-upload' },
        empty: { show: '.b-upload__hint' },
      emptyQueue: { hide: '.js-upload' },
      list: '.js-files',
      file: {
            tpl: '.js-file-tpl',
         preview: {
                el: '.b-thumb__preview',
            width: 80,
            height: 80
         },
         upload: { show: '.progress', hide: '.b-thumb__rotate' },
         complete: { hide: '.progress' },
         progress: '.progress .bar'
      }
   }
});
EOT;
$this->registerJs($sJs, View::POS_READY, 'fileupload');
*/
        ?>
<?php
/*        <div id="multiupload">
            <!-- form class="b-upload b-upload_multi" action="http://rubaxa.org/FileAPI/server/ctrl.php" method="POST" enctype="multipart/form-data" -->
                <div class="b-upload__hint">Добавить файлы в очередь загрузки, например изображения ;]</div>
                <div class="js-files b-upload__files">
                    <div class="js-file-tpl b-thumb" data-id="<%=uid%>" title="<%-name%>, <%-sizeText%>">
                        <div data-fileapi="file.remove" class="b-thumb__del">✖</div>
                        <div class="b-thumb__preview">
                            <div class="b-thumb__preview__pic"></div>
                        </div>
                        <% if( /^image/.test(type) ){ %>
                        <div data-fileapi="file.rotate.cw" class="b-thumb__rotate"></div>
                        <% } %>
                        <div class="b-thumb__progress progress progress-small"><div class="bar"></div></div>
                        <div class="b-thumb__name"><%-name%></div>
                    </div>
                </div>
                <hr>
                <div class="btn btn-success btn-small js-fileapi-wrapper">
                    <span>Add</span>
                    <!-- input name="filedata" type="file" -->
                    <?= $form
                        ->field($model, 'attachfile[]', ['template' => "{input}", 'options' => ['tag' => 'span']])
                        ->fileInput()
                    ?>
                </div>
                <div class="js-upload btn btn-success btn-small">
                    <span>Upload</span>
                </div>
            <!-- /form -->
        </div>
*/
?>

            <!-- ?= $form
//            ->field($model, 'attachfile[]')
//            ->fileInput(['multiple' => true])
//            ->widget(FileInput::classname(), ['options'=>['multiple' => 'multiple'], 'pluginOptions'=>['uploadUrl' => Url::to(['file/upload']),'allowedFileExtensions' => Yii::$app->params['message.file.ext'],'maxFileCount' => 3,]])
            ->field($model, 'attachfile[]', $aFieldParam['filefield'])
//            ->field($model, 'file[]', $aFieldParam['filefield'])
//            ->fileInput(['multiple' => true])
            ->widget(FileInput::classname(), $aFieldParam['file'])
//            ->hint('Максимальное кол-во файлов: ' . $nFiles . ', Максимальный размер файла: ' . Yii::$app->params['message.file.maxsize'] . ' байт, Допустимые типы файлов: ' . implode(',', Yii::$app->params['message.file.ext']))
/*
        <div class="jFiler">
            <a class="file_input">
                <i class="icon-jfi-paperclip"></i>
                Attach a file
            </a>
        </div>

*/
        ? -->
        <?= $form
            ->field($model, 'attachfile[]', ['options' => ['class' => 'form-group']])
            ->fileInput(['multiple' => true])
/*            ->widget(
                FileInput::classname(),
                $aFieldParam['file']
/*                [
                    'options'=>[
//                        'accept'=>'image/*',
                        'multiple'=>true
                    ],
                    'pluginOptions'=>[
                        'uploadUrl' => Url::to(['file/upload']),
                        'allowedFileExtensions'=>['jpg','gif','png','zip']
                    ]
                ]
            ) */
        ?>
    <?php
$sUploadUrl = Url::to(['message/upload', 'id'=>$model->msg_id]);
$sExt = '["' . implode('","', Yii::$app->params['message.file.ext']) . '"]';
$nMaxSize = Yii::$app->params['message.file.maxsize'] / 1000000;
$sJs = <<<EOT
$('#message-attachfile').filer({
        limit: {$nFiles},
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
/*
        uploadFile: {
            url: "message/upload",
            data: {},
            type: 'POST',
            enctype: 'multipart/form-data',
            beforeSend: function(){},
            success: function(data, el){
                var parent = el.find(".jFiler-jProgressBar").parent();
                el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                    $("<div class=\"jFiler-item-others text-success\"><i class=\"icon-jfi-check-circle\"></i> Success</div>").hide().appendTo(parent).fadeIn("slow");
                });
            },
            error: function(el){
                var parent = el.find(".jFiler-jProgressBar").parent();
                el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                    $("<div class=\"jFiler-item-others text-error\"><i class=\"icon-jfi-minus-circle\"></i> Error</div>").hide().appendTo(parent).fadeIn("slow");
                });
            },
            statusCode: {},
            onProgress: function(){},
            onComplete: function(){}
        },
*/
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
            button: "Choose Files",
            feedback: "Choose files To Upload",
            feedback2: "files were chosen",
            drop: "Drop file here to Upload",
            removeConfirmation: "Are you sure you want to remove this file?",
            errors: {
                filesLimit: "Only {{fi-limit}} files are allowed to be uploaded.",
                filesType: "Only {{fi-extension}} are allowed to be uploaded.",
                filesSize: "{{fi-name}} is too large! Please upload file up to {{fi-maxSize}} MB.",
                filesSizeAll: "Files you've choosed are too large! Please upload files up to {{fi-maxSize}} MB."
            }
        }
    });
EOT;

/*
$sJs = <<<EOT
        $('.file_input').filer({
            showThumbs: true,
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
                                            <li><span class="jFiler-item-others">{{fi-icon}} {{fi-size2}}</span></li>\
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
                itemAppendToEnd: true,
                removeConfirmation: true,
                _selectors: {
            list: '.jFiler-item-list',
                    item: '.jFiler-item',
                    progressBar: '.bar',
                    remove: '.jFiler-item-trash-action',
                }
            },
            addMore: true,
        });
EOT;
*/
        $this->registerJs($sJs, View::POS_READY, 'jqueryfiler');

    endif; // if( $nFiles > 0 ):
    ?>
    <div class="clearfix"></div>

    <div class="form-group">
        <label for="message-msg_pers_text" class="control-label col-sm-2">&nbsp;</label>
        <div class="col-sm-6">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        </div>
        <div class="col-sm-4">
            <?php
            // показываем кнопу для вывода обращения
            echo Html::a('Текст обращения', '#', ['class' => 'btn btn-success', 'id'=>'idshowmessage']);
            $this->registerJs('jQuery("#idshowmessage").on("click", function(event) { event.preventDefault(); $("#messagedata").modal("show"); return false; });', View::POS_READY, 'myKey');
            ?>
        </div>
    </div>
    <div class="form-group">
        <label for="message-msg_pers_text" class="control-label col-sm-2">&nbsp;</label>
        <div class="col-sm-6">
            <?php
            foreach($aOp As $k=>$aData):
            ?>
                <?= Html::submitButton(
                'Сохранить и ' . $aData,
                ['class' => 'btn btn-default changeflag', 'id' => 'buttonsave_' . $k, 'style' => 'margin-bottom: 1em;'])
                ?>
            <?php
            endforeach;
            ?>
        </div>
    </div>


    <?php ActiveForm::end();
    $sFlagId = Html::getInputId($model, 'msg_flag');

    // Меняем флаг сообщения в зависимости от нажатой кнопки
    $sJs =  <<<EOT
var oButtons = jQuery('.changeflag'),
    oFlag = jQuery("#{$sFlagId}");

oButtons.on("click", function(event){
    event.preventDefault();
    var ob = jQuery(this),
        nFlag = parseInt(ob.attr("id").split("_")[1]);
//    console.log("id = " + ob.attr("id").split("_")[1]);
    oFlag.val(nFlag);
    jQuery("#message-form").submit();
    return true;
});
EOT;

    $this->registerJs($sJs, View::POS_READY, 'toggleuserpart');

    ?>


    <?php
        // Окно для обращения
        Modal::begin([
            'header' => 'Обращение № ' . $model->msg_id,
            'id' => 'messagedata',
        ]);
    /*        'toggleButton' => [
                'label' => 'Текст обращения',
                'class' => 'btn btn-success',
            ],
    */
    ?>

    <?=
    $this->render(
        '_view',
        [
            'model' => $model,
        ]
    )
    ?>

    <?php
    Modal::end();
    ?>


</div>
