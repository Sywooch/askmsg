<?php

use yii\helpers\Html;
// use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\MaskedInput;
use yii\web\JsExpression;
use yii\web\View;
use yii\bootstrap\Modal;
use yii\captcha\Captcha;

use kartik\select2\Select2;

use app\models\Msgflags;
use app\models\User;
use app\models\Rolesimport;
use app\models\Tags;
use app\models\Message;
use app\models\File;
use app\assets\HelperscriptAsset;
use app\assets\JqueryfilerAsset;

use vova07\imperavi\Widget;


/* @var $this yii\web\View */
/* @var $model app\models\Message */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Проверка обращения № ' . $model->msg_id;
$this->params['breadcrumbs'] = [];

HelperscriptAsset::register($this);
JqueryfilerAsset::register($this);


echo '<!-- ';
if( $model->hasErrors() ) {
    echo str_replace("\n", "<br />\n", print_r($model->getErrors(), true));
}
else {
    echo "No errors";
}
echo '-->' . "\n";

$sFlagId = Html::getInputId($model, 'msg_flag');
$sRemarkId = Html::getInputId($model, 'msg_empl_remark');
$sMsgTextId = Html::getInputId($model, 'msg_pers_text');
$nMsgTextLen = Message::MAX_PERSON_TEXT_LENGTH;

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

// Показываем/скрываем сообщение пользователя и ответ
$sJs .=  <<<EOT
var getDestObj = function(obLink) {
        var id = obLink.attr("id"),
            dest = id.split("_").pop();
        return jQuery("#id_" + dest);
    },
    setToggleTitleByObj = function(obLink){
        var aText = obLink.text().split(" "),
            oDest = getDestObj(obLink);
        aText[0] = oDest.is(":visible") ? "Скрыть" : "Показать";
        obLink.text(aText.join(" "));
    },
    aLinks = jQuery(".togglepart");
aLinks.on("click", function(event){
    var obLink = jQuery(this),
        oDest = getDestObj(obLink);
    event.preventDefault();
    oDest.toggle();
    setToggleTitleByObj(obLink);
    return false;
});
aLinks.each(function(index, el){
    var obLink = jQuery(this);
    setToggleTitleByObj(obLink);
});
EOT;

// Меняем флаг сообщения в зависимости от нажатой кнопки
$sJs .=  <<<EOT
var oButtons = jQuery('.changeflag'),
    oFlag = jQuery("#{$sFlagId}"),
    oRemark = jQuery("#{$sRemarkId}");

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
// функция форматирования результатов в список для select2
$sJs =  <<<EOT
var formatSelect = function(item, text, description) {
    return  item[text] + "<span class=\\"description\\">" + item[description] + "</span>";
}

EOT;
$this->registerJs($sJs, View::POS_END , 'showselectpart');


$aFieldParam = [
    'subjectfield' => [
        'horizontalCssClasses' => [
            'label' => 'col-sm-1',
            'offset' => 'col-sm-offset-1',
            'wrapper' => 'col-sm-11',
        ],
    ],
    'orgfield' => [
//        'template' => "{input}\n{hint}\n{error}",
        'horizontalCssClasses' => [
            'label' => 'col-sm-1',
            'offset' => 'col-sm-offset-1',
            'wrapper' => 'col-sm-11',
        ],
        'inputOptions' => [
            'disabled' => true,
        ]
    ],
    'answerfield' => [
        'template' => "{input}\n{hint}\n{error}",
        'horizontalCssClasses' => [
//            'label' => 'col-sm-1',
//            'offset' => 'col-sm-offset-1',
            'wrapper' => 'col-sm-12',
        ],
        'inputOptions' => [
            'disabled' => true,
        ]
    ],
    'textfield' => [
//        'template' => "{input}\n{hint}\n{error}",
        'horizontalCssClasses' => [
            'label' => 'col-sm-1',
            'offset' => 'col-sm-offset-1',
//            'offset' => '',
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
];
?>
<div class="message-curatortest">
    <h4 style="margin-bottom: 30px; margin-top: 0;"><?= Html::encode($this->title) ?></h4>
<div class="message-form">

    <?php $form = ActiveForm::begin([
            'id' => 'message-form',
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
    ]);


    echo $form->errorSummary([$model]);

//    <?php
    /************************************************************************************************
     *
     * Часть пользователя
     *
     */
    ?>
    <div id="id_userformpart" style="display: none; clear: both; border: 1px solid #777777; border-radius: 4px; background-color: #f8f8f8; padding-top: 2em; padding-bottom: 2em; margin-bottom: 2em;">

        <div class="col-sm-6">
            <div class="form-group field-message-msg_subject">
            <label class="control-label col-sm-2" for="message-msg_subject">Тема</label>
            <div class="col-sm-10">
                <input class="form-control" type="text" readonly="" value="<?= $model->subject !== null ? Html::encode($model->subject->tag_title) : '' ?>">
            </div>
            </div>
            <?= '' /* $form
                ->field($model, 'msg_subject',$aFieldParam['subjectfield'])
                ->textInput(['disabled'=>true, 'readonly'=>true])*/ ?>
        </div>

        <div class="col-sm-6">
            <div class="form-group field-message-msg_subject">
                <label class="control-label col-sm-2" for="message-msg_subject">Учреждение</label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" readonly="" value="<?= Html::encode($model->msg_pers_org) ?>">
                </div>
            </div>
            <?= '' /* $form
                ->field($model, 'msg_pers_org', $aFieldParam['orgfield'])
                ->textInput(['maxlength' => 255])*/ ?>
        </div>

        <div class="col-sm-6">
            <div class="form-group field-message-msg_subject">
                <label class="control-label col-sm-2" for="message-msg_subject">Автор</label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" readonly="" value="<?= Html::encode($model->getFullName()) ?>">
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group field-message-msg_subject">
                <label class="control-label col-sm-2" for="message-msg_subject">Контакты</label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" readonly="" value="<?= Html::encode($model->msg_pers_phone . '       ' . $model->msg_pers_email) ?>">
                </div>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="col-sm-12">
            <?=
            $form
                ->field($model, 'msg_pers_text', $aFieldParam['textfield'])
                ->widget(Widget::className(), [
                    'settings' => [
                        'lang' => 'ru',
                        'minHeight' => 200,
                        'buttons' => ['formatting', 'bold', 'italic', 'underline', 'deleted', 'unorderedlist', 'orderedlist', 'link', 'alignment'], // 'outdent', 'indent', 'image',
                        'plugins' => [
//                       'clips',
                            'fullscreen',
                        ]
                    ]
                ])
            ?>
        </div>

        <?php
        $aFiles = $model->getUserFiles(true);
        if( count($aFiles) > 0 ):
            ?>
            <div class="col-sm-12">
                <label for="message-msg_pers_text" class="control-label col-sm-1">Файлы</label>
                <div class="col-sm-11">
                    <?php
                    foreach($aFiles As $oFile):
                        /** @var File  $oFile */
                        ?>
                        <div class="btn btn-default">
                            <?= Html::a( Html::encode($oFile->file_orig_name), $oFile->getUrl()) ?>
                        </div>
                    <?php
                    endforeach;
                    ?>
                    <div class="clearfix"></div>
                </div>
            </div>
        <?php
        endif;
        ?>

        <div class="clearfix"></div>

    </div>
<?php

/**
 *
 * Окончание части пользователя
 *
 ************************************************************************************************/
?>

    <?php
    /************************************************************************************************
     *
     * Часть модератора
     *
     */
    ?>
        <?= $form->field($model, 'msg_flag', ['template' => "{input}", 'options' => ['tag' => 'span']])->hiddenInput();  ?>


        <div class="col-sm-5">
            <?= $form
                ->field($model, 'employer')
                ->textInput(['disabled'=>true, 'readonly'=>true])
            ?>
        </div>

        <div class="col-sm-5">
            <?= $form
                ->field($model, 'msg_empl_command')
                ->textarea(['disabled'=>true, 'readonly'=>true]);
            ?>

        </div>

        <div class="col-sm-2">
            <a href="#" class="btn btn-default togglepart" id="toggle_userformpart" style="">Показать Обращение</a>
        </div>


    <div style="clear: both;">
                <?= $form
                    ->field(
                        $model,
                        'msg_answer',
                        $aFieldParam['answerfield'])
                    ->widget(Widget::className(), [
                        'settings' => [
                            'lang' => 'ru',
                            'minHeight' => 200,
                            'buttons' => ['formatting', 'bold', 'italic', 'deleted', 'unorderedlist', 'orderedlist', 'link', 'alignment'], // 'outdent', 'indent', 'image',
                            'plugins' => [
//                       'clips',
                                'fullscreen',
                            ]
                        ]
                    ]) ?>

            <?php
            $aFiles = $model->getUserFiles(false);
            if( count($aFiles) > 0 ):
            ?>
                <div class="clearfix"></div>
                <label for="message-msg_pers_text" class="control-label col-sm-1">Файлы</label>
                <div class="col-sm-11">
                    <?php
                    foreach($aFiles As $oFile):
                        /** @var File  $oFile */
                    ?>
                        <div class="btn btn-default">
                            <?= Html::a( Html::encode($oFile->file_orig_name), $oFile->getUrl()) ?>
                            <?= Html::a('<span class="glyphicon glyphicon-remove"></span>', ['file/remove', 'id' => $oFile->file_id], ['class'=>"link_with_confirm", 'title'=>'Удалить файл ' . Html::encode($oFile->file_orig_name)]) ?>
                        </div>
                    <?php
                        //                    <!-- ?= Html::a('<span class="glyphicon glyphicon-remove"></span>', ['file/delete', 'id' => $oFile->file_id]) ? -->
                    endforeach;
                    ?>
                    <div class="clearfix"></div>
                </div>
            <?php
            endif;
            ?>

            </div>

    <?php
    /**
     *
     * Окончание части модератора
     *
     ************************************************************************************************/
    ?>


    <div class="col-sm-6">
        <?= $form
            ->field($model, 'msg_empl_remark')
            ->textarea()
            ->hint('Текст замечания будет виден только исполнителям и модератору');
        ?>
    </div>

    <div class="col-sm-6">
        <div class="form-group">
            <?php
            /**
             *
             * Тут кусочек для кнопок модератора, чтобы он нажимал и менял флаг
             *
             */
                $aOp = array_reduce(
                    Msgflags::getStateTransCurator($model->msg_flag),
                    function ( $carry , $item ) {
                        $sTitle = Msgflags::getStateTitle($item, 'fl_command');
                        if( $sTitle != '' ) {
                            $aFlagData = Msgflags::getStateData($item);
                            $carry[$item] = ['title' => $sTitle, 'hint' => isset($aFlagData['fl_hint']) ? $aFlagData['fl_hint'] : '--'];
                        }
                        return $carry;
                    },
                    []
                );
                    foreach($aOp As $k=>$aData):
                        if( $k == 1 ) {
                            continue;
                        }
                ?>
                        <div id="<?= "buttongroup_" . $k ?>">
                            <div class="col-sm-6">
                            <?= Html::submitButton(
                            $aData['title'], // 'Сохранить и ' .
                            ['class' => 'btn btn-default btn-block changeflag', 'id' => 'buttonsave_' . $k, 'style' => 'margin-bottom: 1em;']) ?>
                            </div>
                            <div class="col-sm-6 help-block">
                                <?= $aData['hint'] ?>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                <?php
                    endforeach;
                ?>
            <?php

            /**
                 *
                 * Окончание кнопок модератора
                 *
                 */
                ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</div>
