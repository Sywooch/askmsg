<?php

use yii\helpers\Html;
// use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\MaskedInput;
use yii\web\JsExpression;
use yii\web\View;

use app\models\Regions;
use app\models\Msgflags;

use kartik\typeahead\Typeahead;

/* @var $this yii\web\View */
/* @var $model app\models\Message */
/* @var $form yii\widgets\ActiveForm */

/*
    <?= $form->field($model, 'msg_createtime')->textInput() ?>
    <?= $form->field($model, 'msg_active')->textInput() ?>
    <?= $form->field($model, 'msg_comment')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'msg_empl_id')->textInput() ?>
    <?= $form->field($model, 'msg_empl_command')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'msg_empl_remark')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'msg_answer')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'msg_answertime')->textInput() ?>
    <?= $form->field($model, 'msg_oldcomment')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'msg_flag')->textInput() ?>

*/
?>

<div class="message-form">

    <?php $form = ActiveForm::begin([
            'id' => 'message-form',
            'layout' => 'horizontal',
            'fieldConfig' => [
//                'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                'horizontalCssClasses' => [
                    'label' => 'col-sm-3',
                    'offset' => 'col-sm-offset-3',
                    'wrapper' => 'col-sm-9',
//                    'error' => '',
//                    'hint' => '',
                ],
            ],
    ]);
/*
    <div class="col-sm-4">
    </div>
    <div class="clearfix"></div>

*/
    ?>

    <?php
    /************************************************************************************************
     *
     * Часть модератора
     *
     */
    if( $model->scenario == 'moderator' ):

/*
        <div class="col-sm-6">
            <?= $form
                ->field($model, 'msg_flag')
                ->dropDownList(
                    array_reduce(
                        Msgflags::getStateTrans($model->msg_flag),
                        function ( $carry , $item ) {
                            $sTitle = Msgflags::getStateTitle($item, 'fl_command');
                            if( $sTitle != '' ) {
                                $carry[$item] = $sTitle;
                            }
                            return $carry;
                        },
                        []
                    )
                )
            ?>
        </div>
*/
?>

        <div class="col-sm-6">
            <?= $form->field($model, 'employer')->widget(
                Typeahead::classname(),
                [
                    'scrollable' => true,
                    'dataset' => [
                        [
                            'remote' => [
                                'url' => Url::to(['user/answerlist', 'query'=>'QRY']),
                                'wildcard' => 'QRY',
                            ],
                            'displayKey' => 'val',

                            'templates' => [
                                'suggestion' => new JsExpression("Handlebars.compile('<p>{{val}}<br /><span style=\"color: #777777;\">{{pos}}</span></p>')"),
                            ],

                        ]
                    ],
                    'pluginOptions' => [
                        'highlight' => true,
                        'minLength' => 2,
                    ],
                    'pluginEvents' => [
                        'typeahead:selected' => 'function(event, ob) { jQuery("#'.Html::getInputId($model, 'msg_empl_id').'").val(ob.id); console.log("-- typeahead:selected --"); console.log(event); console.log(ob); }',
                    ],
                ]
            ) ?>
            <?= $form->field($model, 'msg_empl_id', ['template' => "{input}", 'options' => ['tag' => 'span']])->hiddenInput();  ?>
            <?= $form->field($model, 'msg_flag', ['template' => "{input}", 'options' => ['tag' => 'span']])->hiddenInput();  ?>
        </div>

        <div class="col-sm-6">
            <?= $form
                ->field($model, 'msg_empl_command')
                ->textarea();
            ?>
        </div>

        <div class="col-sm-6">
            <?= $form
                ->field($model, 'msg_comment')
                ->textarea();
            ?>
        </div>

        <div class="col-sm-6">
            <?= $form
                ->field($model, 'msg_empl_remark')
                ->textarea();
            ?>
        </div>

        <?php if( !empty($model->msg_answer)  ): ?>
        <div class="col-sm-12 thumbnail">
            <label for="message-msg_pers_text" class="control-label col-sm-1">Ответ</label>
            <div style="clear: both;">
            <?= Html::encode($model->msg_answer) ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="col-sm-6">
            <div class="form-group">
                <label for="message-msg_pers_text" class="control-label col-sm-3">&nbsp;</label>
                <div class="col-sm-9">
                    <a href="#" class="btn btn-default" id="toggleuserformpart">Сообщение пользователя</a>
                </div>
            </div>

        </div>
    <?php
    endif; // if( $model->scenario == 'moderator' ):
    /**
     *
     * Окончание части модератора
     *
     ************************************************************************************************/
    ?>


    <?php
    /************************************************************************************************
     *
     * Часть пользователя
     *
     */
    if( $model->scenario == 'moderator' ):
    ?>
        <div id="hideuserformpart" style="display: none; clear: both; border: 1px solid #777777; border-radius: 4px; background-color: #eeeeee; padding-top: 2em; margin-bottom: 2em;">
    <?php
    endif; // if( $model->scenario == 'moderator' ):
    ?>
    <div class="col-sm-4">
        <?= $form->field($model, 'msg_pers_lastname')->textInput(['maxlength' => 255]) ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'msg_pers_name')->textInput(['maxlength' => 255]) ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'msg_pers_secname')->textInput(['maxlength' => 255]) ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'msg_pers_email')->textInput(['maxlength' => 255]) ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'msg_pers_phone')->widget(MaskedInput::className(),[
            'name' => 'msg_pers_phone',
            'mask' => '+7(999) 999-99-99'
        ]) ?>
    </div>

    <div class="clearfix"></div>

    <div class="col-sm-4">
        <?= $form
            ->field($model, 'msg_pers_region')
            ->dropDownList(
                ArrayHelper::map(
                    Regions::find()
                        ->where(['reg_active'=>1])
                        ->orderBy(['reg_name' => SORT_ASC])
                        ->all(),
                    'reg_id',
                    'reg_name'
                )
            ) ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field(
            $model,
            'msg_pers_org'
/*            ,
            [
//                'options' => [
//                    'tag' => null,
//                    // 'placeholder' => $model->getAttributeLabel('demo'),
//                ],
            ]*/)
            ->textInput(['maxlength' => 255]) ?>
    </div>


    <div class="clearfix"></div>

    <div class="col-sm-12">
    <?= $form->field(
        $model,
        'msg_pers_text',
        [
//            'template' => "{input}\n{hint}\n{error}",
            'horizontalCssClasses' => [
                'label' => 'col-sm-1',
                'offset' => 'col-sm-offset-1',
                'wrapper' => 'col-sm-11',
            ],
        ])->textarea(['rows' => 6]) ?>
    </div>

    <div class="clearfix"></div>

    <?php
    if( $model->scenario == 'moderator' ):
    ?>
        </div>
    <?php
    endif; // if( $model->scenario == 'moderator' ):
    /**
     *
     * Окончание части пользователя
     *
     ************************************************************************************************/
    ?>

    <div class="col-sm-12">
        <div class="form-group">
                <?php
                /**
                 *
                 * Тут кусочек для кнопок модератора, чтобы он нажимал и менял флаг
                 *
                 */
                    if( $model->scenario == 'moderator' ):
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
                        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group" style="margin-top: 2em;">
                    <?php
                        foreach($aOp As $k=>$aData):
                    ?>
                            <?= Html::submitButton(
                                'Сохранить и ' . $aData,
                                ['class' => 'btn btn-default changeflag', 'id' => 'buttonsave_' . $k, 'style' => 'margin-bottom: 1em;'])
                            ?>
                            <?= '' /*Html::a('Сохранить и ' . $aData, '#', ['class' => 'btn btn-primary changeflag', 'id' => 'buttonsave_' . $k])*/ ?>
                    <?php
                        endforeach;
                    ?>
                <?php
                /**
                 *
                 * Окончание кнопок модератора
                 *
                 */
                    else:
                ?>
                    <label for="message-msg_pers_text" class="control-label col-sm-1">&nbsp;</label>
                    <div class="col-sm-6">
                        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Изменить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                    </div>
                <?php
                    endif; // if( $model->scenario == 'moderator' ):
                ?>
        </div>
    </div>

    <?php ActiveForm::end();


    $sFlagId = Html::getInputId($model, 'msg_flag');
    $sCommandId = Html::getInputId($model, 'msg_empl_command');
    $sRemarkId = Html::getInputId($model, 'msg_empl_remark');

// Показываем/скрываем сообщение пользователя
    $sJs =  <<<EOT
var oUserPart = jQuery("#hideuserformpart");
jQuery('#toggleuserformpart').on("click", function(event){
    event.preventDefault();
    oUserPart.toggle();
    return false;
});
EOT;

// Меняем флаг сообщения в зависимости от нажатой кнопки
    $sJs .=  <<<EOT
var oButtons = jQuery('.changeflag'),
    oFlag = jQuery("#{$sFlagId}"),
    oCommand = jQuery("#{$sCommandId}"),
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

    // Фильтруем видимость кнопок в зависимости от смены состояния полей замечаний и поручений
    // новая запись: оставляем кнопки с поручениями, если заполнено поручение
    $nFlagNewMsg = Msgflags::MFLG_NEW;
    $nFlagInstr = Msgflags::MFLG_SHOW_INSTR;
    $nFlagInstrInt = Msgflags::MFLG_INT_INSTR;
    $sJs .=  <<<EOT
var filterButtons = function() {
    if( {$nFlagNewMsg} == parseInt(oFlag.val()) ) {
        if( oCommand.val().length > 0 ) {
            oButtons.each(function(index, ob){
                var ob = jQuery(this), nId = parseInt(ob.attr("id").split("_")[1])
                console.log("flag = " + nId + " - " + ob.attr("id"));
                if( (nId != {$nFlagInstrInt}) && nId != {$nFlagInstr} ) {
                    ob.hide();
                }
                else {
                    ob.show();
                }
            });
        }
        else {
            oButtons.show();
        }
        console.log("Command = " + oCommand.val());
    }
};

oCommand.on("keyup", function(event){
    filterButtons();
});
EOT;

        $this->registerJs($sJs, View::POS_READY, 'toggleuserpart');
    ?>

</div>
