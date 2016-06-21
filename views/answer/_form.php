<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Answer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="answer-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ans_created')->textInput() ?>

    <?= $form->field($model, 'ans_text')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'ans_remark')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'ans_type')->textInput() ?>

    <?= $form->field($model, 'ans_state')->textInput() ?>

    <?= $form->field($model, 'ans_ap_id')->textInput() ?>

    <?= $form->field($model, 'ans_us_id')->textInput() ?>

    <?= $form->field($model, 'ans_mark')->textInput() ?>

    <?= $form->field($model, 'ans_mark_comment')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
