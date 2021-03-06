<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\File */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="file-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'file_time')->textInput() ?>

    <?= $form->field($model, 'file_orig_name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'file_msg_id')->textInput() ?>

    <?= $form->field($model, 'file_user_id')->textInput() ?>

    <?= $form->field($model, 'file_size')->textInput() ?>

    <?= $form->field($model, 'file_type')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'file_name')->textInput(['maxlength' => 255]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
