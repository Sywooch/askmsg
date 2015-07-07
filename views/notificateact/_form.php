<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Notificateact */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="notificateact-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ntfd_message_age')->textInput() ?>

    <?= $form->field($model, 'ntfd_operate')->textInput() ?>

    <?= $form->field($model, 'ntfd_flag')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
