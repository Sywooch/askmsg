<?php

use yii\helpers\Html;
// use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\MaskedInput;
use yii\web\JsExpression;
use yii\web\View;

use kartik\select2\Select2;

use app\models\Regions;
use app\models\Msgflags;
use app\models\User;
use app\models\Rolesimport;
use app\models\Tags;
use app\models\Message;

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
    ?>
        <?= $form->field($model, 'msg_flag', ['template' => "{input}", 'options' => ['tag' => 'span']])->hiddenInput();  ?>


        <div class="col-sm-6">
            <?php
                $aAnsw = User::getGroupUsers(Rolesimport::ROLE_ANSWER_DOGM, '', '{{val}}');
            ?>
            <?= $form
                ->field($model, 'msg_empl_id')
//                ->field($model, 'employer')
                ->widget(Select2::classname(), [
                    'data' => $aAnsw,
                    'language' => 'ru',
                    'options' => ['placeholder' => 'Выберите ответчика ...'],
                    'pluginOptions' => [
                        'allowClear' => true,
//                        'formatResult' => new JsExpression('function(object, container, query){ console.log("format: ", object, container, query); container.append(object.text);  }'),
                    ],
                    'pluginEvents' => [
//                        'change' => 'function(event) { jQuery("#'.Html::getInputId($model, 'msg_empl_id').'").val(event.val); console.log("change", event); }',
//                        'select2-selecting' => 'function(event) { console.log("select2-selecting", event); }',
                    ],
                ]);
            ?>
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

        <div class="col-sm-6">
            <?= $form
                ->field($model, 'answers')
                ->widget(Select2::classname(), [
                    'data' => $aAnsw,
                    'language' => 'ru',
                    'options' => [
                        'multiple' => true,
                        'placeholder' => 'Выберите соответчика ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ])
            //                ->dropDownList($aAnsw, ['multiple' => true])
            ?>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <label for="message-msg_pers_text" class="control-label col-sm-3">&nbsp;</label>
                <div class="col-sm-4">
                    <a href="#" class="btn btn-default togglepart" id="toggle_userformpart" style="margin-bottom: 14px;">Обращение</a>
                </div>

        <?php if( !empty($model->msg_answer)  ): ?>
                <div class="col-sm-4">
                    <a href="#" class="btn btn-default togglepart" id="toggle_answer">Ответ</a>
                </div>
        <?php endif; ?>

            </div>

        </div>

        <?php if( !empty($model->msg_answer)  ): ?>
        <div class="col-sm-12 thumbnail " id="id_answer" style="display: none;">
            <label for="message-msg_pers_text" class="control-label col-sm-1">Ответ</label>
            <div style="clear: both;">
            <?= $model->msg_answer ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="col-sm-6">
            <?= $form
                ->field($model, 'alltags')
                ->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(Tags::getTagslist(Tags::TAGTYPE_TAG), 'tag_id', 'tag_title'),
                    'language' => 'ru',
                    'options' => [
                        'multiple' => true,
//                        'tags' => true,
                        'placeholder' => 'Выберите теги ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ])
            //                ->dropDownList($aAnsw, ['multiple' => true])
            ?>
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
        <div id="id_userformpart" style="display: none; clear: both; border: 1px solid #777777; border-radius: 4px; background-color: #eeeeee; padding-top: 2em; margin-bottom: 2em;">
    <?php
    endif; // if( $model->scenario == 'moderator' ):
    ?>

    <div class="col-sm-12">
        <?= $form
            ->field(
                $model,
                'msg_subject',
                [
//            'template' => "{input}\n{hint}\n{error}",
                    'horizontalCssClasses' => [
                    'label' => 'col-sm-1',
                    'offset' => 'col-sm-offset-1',
                    'wrapper' => 'col-sm-11',
                ],
            ])
            ->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Tags::getTagslist(Tags::TAGTYPE_SUBJECT), 'tag_id', 'tag_title'),
                'language' => 'ru',
                'options' => [
//                    'multiple' => true,
                    'placeholder' => 'Выберите тему сообщения ...',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ])

        ?>
    </div>

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
        ])
        ->textarea(['rows' => 6]) ?>
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
                /**
                 *
                 * Окончание кнопок модератора
                 *
                 */
                    else:
                ?>
                    <label for="message-msg_pers_text" class="control-label col-sm-1">&nbsp;</label>
                    <div class="col-sm-6">
                        <?= Html::submitButton($model->isNewRecord ? 'Отправить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
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

// Показываем/скрываем сообщение пользователя
    $sJs .=  <<<EOT
//var oUserPart = jQuery(".togglepart");
jQuery(".togglepart").on("click", function(event){
    var ob = jQuery(this),
        id = ob.attr("id"),
        dest = id.split("_").pop();
    event.preventDefault();
    jQuery("#id_" + dest).toggle();
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
