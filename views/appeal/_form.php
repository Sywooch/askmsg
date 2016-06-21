<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Appeal */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="appeal-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ap_created')->textInput() ?>

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

    <?= $form->field($model, 'ap_ans_state')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
