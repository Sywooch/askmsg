<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\NotificateactSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="notificateact-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'ntfd_id') ?>

    <?= $form->field($model, 'ntfd_message_age') ?>

    <?= $form->field($model, 'ntfd_operate') ?>

    <?= $form->field($model, 'ntfd_flag') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
