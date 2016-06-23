<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MediateanswerSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mediateanswer-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'ma_id') ?>

    <?= $form->field($model, 'ma_created') ?>

    <?= $form->field($model, 'ma_text') ?>

    <?= $form->field($model, 'ma_remark') ?>

    <?= $form->field($model, 'ma_msg_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
