<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SupportSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="support-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'sup_id') ?>

    <?= $form->field($model, 'sup_createtime') ?>

    <?= $form->field($model, 'sup_message') ?>

    <?= $form->field($model, 'sup_empl_id') ?>

    <?= $form->field($model, 'sup_active') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
