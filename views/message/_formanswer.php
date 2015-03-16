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
use app\assets\JqueryfilerAsset;
use app\assets\HelperscriptAsset;


/* @var $this yii\web\View */
/* @var $model app\models\Message */
/* @var $form yii\widgets\ActiveForm */

ListdataAsset::register($this);
JqueryfilerAsset::register($this);
HelperscriptAsset::register($this);

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

$aFieldParam = [
    'filefield' => [
//            'template' => "{input}\n{hint}\n{error}",
        'horizontalCssClasses' => [
            'label' => 'col-sm-2',
            'offset' => 'col-sm-offset-2',
            'wrapper' => 'col-sm-10',
        ],
        'hintOptions' => [
            'class' => 'help-block col-sm-10 col-sm-offset-2',
        ],
    ],
];

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
                        <?= Html::a('<span class="glyphicon glyphicon-remove"></span>', ['file/remove', 'id' => $oFile->file_id], ['class'=>"link_with_confirm", 'title'=>'Удалить файл ' . Html::encode($oFile->file_orig_name)]) ?>
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
    ?>

        <?= $form
            ->field($model, 'file[]', $aFieldParam['filefield'])
            ->fileInput(['multiple' => true])
            ->hint('Максимальный размер файла: '
                . Yii::$app->params['message.file.maxsize']
                . ' байт, Допустимые типы файлов: '
                . implode(',', Yii::$app->params['message.file.ext'])
                . '. Количество файлов: ' . $nFiles
            )
        ?>

    <?php
$sExt = '["' . implode('","', Yii::$app->params['message.file.ext']) . '"]';
$nMaxSize = Yii::$app->params['message.file.maxsize'] / 1000000;
$sJs = <<<EOT
$('#message-file').filer({
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
                filesType: "Файлы только типов {{fi-extension}} разрешены к загрузке.",
                filesSize: "{{fi-name}} слишком большой! Выберите файл до {{fi-maxSize}} MB.",
                filesSizeAll: "Слишко большие файлы выбрали! Пожалуйста ограничьте их размер {{fi-maxSize}} MB."
            }
        }
    });
EOT;

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
