<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AnswerSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="answer-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'ans_id') ?>

    <?= $form->field($model, 'ans_created') ?>

    <?= $form->field($model, 'ans_text') ?>

    <?= $form->field($model, 'ans_remark') ?>

    <?= $form->field($model, 'ans_type') ?>

    <?php // echo $form->field($model, 'ans_state') ?>

    <?php // echo $form->field($model, 'ans_ap_id') ?>

    <?php // echo $form->field($model, 'ans_us_id') ?>

    <?php // echo $form->field($model, 'ans_mark') ?>

    <?php // echo $form->field($model, 'ans_mark_comment') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
