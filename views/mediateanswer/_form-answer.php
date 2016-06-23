<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\View;
use yii\bootstrap\Modal;

use app\assets\HelperscriptAsset;
use app\assets\ListdataAsset;
use app\models\Msgflags;

use vova07\imperavi\Widget;


/* @var $this yii\web\View */
/* @var $model app\models\Mediateanswer */
/* @var $message app\models\Message */
/* @var $form yii\widgets\ActiveForm */

ListdataAsset::register($this);
HelperscriptAsset::register($this);

?>

<div class="mediateanswer-form">

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


    <p>Текущее состояние: <?= Html::encode($message->flag->getStateTitle($message->msg_flag)) ?></p>
    <?php
    if( strlen($message->msg_empl_command) > 0 ):
        ?>
        <div class="alert alert-warning" role="alert"><strong>Поручение:</strong> <?= Html::encode($message->msg_empl_command) ?></div>
    <?php
    endif;
    ?>

    <?php
    if( strlen($message->msg_comment) > 0 ):
        ?>
        <div class="alert alert-warning" role="alert"><strong>Комментарий:</strong> <?= Html::encode($message->msg_comment) ?></div>
    <?php
    endif;
    ?>

    <?php
    if( strlen($model->ma_remark) > 0 ):
        ?>
        <div class="alert alert-danger" role="alert"><strong>Замечание:</strong> <?= Html::encode($model->ma_remark) ?></div>
    <?php
    endif;
    ?>

    <?php
    if( $model->ma_text == '' ) {
        $model->ma_text = 'Уважаем'
            . ($message->tryGender() == 'ж' ? 'ая' : 'ый')
            . ' '
            . $message->getShortName()
            . "!\n\nС уважением, "
            . Yii::$app->user->identity->getFullName()
            . '.';
    }

    // Фокус на редактор помещаем
    $sJs = 'setTimeout(function() {var oEditor = jQuery(".redactor-editor").first(); oEditor.focus(); /* console.log("Click: ", oEditor); */ }, 500);';
    $this->registerJs($sJs, View::POS_READY, 'focusonimperavi');

    ?>
    <?= $form
        ->field(
            $model,
            'ma_text')
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
    <?= $form->field($model, 'msg_flag', ['template' => "{input}", 'options' => ['tag' => 'span']])->hiddenInput();  ?>

    <div class="form-group">
        <label for="message-msg_pers_text" class="control-label col-sm-2">&nbsp;</label>
        <div class="col-sm-3">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary btn-block']) ?>
        </div>
        <div class="col-sm-3">
            <?php
            // показываем кнопу для вывода обращения
            echo Html::a('Текст обращения', '#', ['class' => 'btn btn-success btn-block', 'id'=>'idshowmessage']);
            $this->registerJs('jQuery("#idshowmessage").on("click", function(event) { event.preventDefault(); $("#messagedata").modal("show"); return false; });', View::POS_READY, 'myKey');
            ?>
        </div>
    </div>

        <?php

        $aOp = array_reduce(
            Msgflags::getStateTransAnswer($message->msg_flag, $message->msg_curator_id !== null),
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

        ?>
        <div class="form-group" style="margin-top: 3em;">
            <?php
            foreach($aOp As $k=>$aData):
                ?>
                <label for="message-msg_pers_text" class="control-label col-sm-2">&nbsp;</label>
                <div id="<?= "buttongroup_" . $k ?>">
                    <div class="col-sm-3">
                        <?= Html::submitButton(
                            $aData['title'], // 'Сохранить и ' .
                            ['class' => 'btn btn-default btn-block changeflag', 'id' => 'buttonsave_' . $k, 'style' => 'margin-bottom: 1em;']) ?>
                    </div>
                    <div class="col-sm-7 help-block">
                        <?= $aData['hint'] ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
            <?php
            endforeach;
            ?>
            <div>
                <label for="message-msg_pers_text" class="control-label col-sm-2">&nbsp;</label>
                <div class="col-sm-3">
                    <?= Html::a(
                        'Вернуться в список обращений',
                        ['answerlist'],
                        ['class' => 'btn btn-default btn-block', 'id' => 'button_go_back', 'style' => 'margin-bottom: 1em;'])
                    ?>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

    <?php
        ActiveForm::end();
    $sFlagId = Html::getInputId($model, 'msg_flag');

    // Меняем флаг сообщения в зависимости от нажатой кнопки
    $sJs =  <<<EOT
var oButtons = jQuery('.changeflag'),
    oFlag = jQuery("#{$sFlagId}");
//    console.log("flag field {{$sFlagId}} : ", oFlag);

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

    $this->registerJs($sJs, View::POS_READY, 'changemshflag');

    ?>


    <?php
    // Окно для обращения
    Modal::begin([
        'header' => 'Обращение № ' . $message->msg_id,
        'id' => 'messagedata',
        'size' => Modal::SIZE_LARGE,
    ]);
    /*        'toggleButton' => [
                'label' => 'Текст обращения',
                'class' => 'btn btn-success',
            ],
    */
    ?>

    <?=
    $this->render(
        '//message/_view01',
        [
            'model' => $message,
        ]
    )
    ?>

    <?php
    Modal::end();
    ?>


</div>
