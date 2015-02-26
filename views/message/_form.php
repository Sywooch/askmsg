<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Message */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="message-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'msg_createtime')->textInput() ?>

    <?= $form->field($model, 'msg_active')->textInput() ?>

    <?= $form->field($model, 'msg_pers_name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'msg_pers_secname')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'msg_pers_lastname')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'msg_pers_email')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'msg_pers_phone')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'msg_pers_org')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'msg_pers_region')->textInput() ?>

    <?= $form->field($model, 'msg_pers_text')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'msg_comment')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'msg_empl_id')->textInput() ?>

    <?= $form->field($model, 'msg_empl_command')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'msg_empl_remark')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'msg_answer')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'msg_answertime')->textInput() ?>

    <?= $form->field($model, 'msg_oldcomment')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'msg_flag')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
