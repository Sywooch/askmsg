<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Msgflags */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="msgflags-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fl_name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'fl_sort')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
