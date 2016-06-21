<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AppealSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="appeal-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'ap_id') ?>

    <?= $form->field($model, 'ap_created') ?>

    <?= $form->field($model, 'ap_next_act_date') ?>

    <?= $form->field($model, 'ap_pers_name') ?>

    <?= $form->field($model, 'ap_pers_secname') ?>

    <?php // echo $form->field($model, 'ap_pers_lastname') ?>

    <?php // echo $form->field($model, 'ap_pers_email') ?>

    <?php // echo $form->field($model, 'ap_pers_phone') ?>

    <?php // echo $form->field($model, 'ap_pers_org') ?>

    <?php // echo $form->field($model, 'ap_pers_region') ?>

    <?php // echo $form->field($model, 'ap_pers_text') ?>

    <?php // echo $form->field($model, 'ap_empl_command') ?>

    <?php // echo $form->field($model, 'ap_comment') ?>

    <?php // echo $form->field($model, 'ap_subject') ?>

    <?php // echo $form->field($model, 'ap_empl_id') ?>

    <?php // echo $form->field($model, 'ap_curator_id') ?>

    <?php // echo $form->field($model, 'ekis_id') ?>

    <?php // echo $form->field($model, 'ap_state') ?>

    <?php // echo $form->field($model, 'ap_ans_state') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
