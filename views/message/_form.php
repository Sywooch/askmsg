<?php

use yii\helpers\Html;
// use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Regions;
use yii\widgets\MaskedInput;


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

    <div class="col-sm-12">
        <div class="form-group">
            <label for="message-msg_pers_text" class="control-label col-sm-1">&nbsp;</label>
            <div class="col-sm-6">
                <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Изменить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
