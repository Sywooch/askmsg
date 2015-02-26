<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MessageSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="message-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'msg_id') ?>

    <?= $form->field($model, 'msg_createtime') ?>

    <?= $form->field($model, 'msg_active') ?>

    <?= $form->field($model, 'msg_pers_name') ?>

    <?= $form->field($model, 'msg_pers_secname') ?>

    <?php // echo $form->field($model, 'msg_pers_lastname') ?>

    <?php // echo $form->field($model, 'msg_pers_email') ?>

    <?php // echo $form->field($model, 'msg_pers_phone') ?>

    <?php // echo $form->field($model, 'msg_pers_org') ?>

    <?php // echo $form->field($model, 'msg_pers_region') ?>

    <?php // echo $form->field($model, 'msg_pers_text') ?>

    <?php // echo $form->field($model, 'msg_comment') ?>

    <?php // echo $form->field($model, 'msg_empl_id') ?>

    <?php // echo $form->field($model, 'msg_empl_command') ?>

    <?php // echo $form->field($model, 'msg_empl_remark') ?>

    <?php // echo $form->field($model, 'msg_answer') ?>

    <?php // echo $form->field($model, 'msg_answertime') ?>

    <?php // echo $form->field($model, 'msg_oldcomment') ?>

    <?php // echo $form->field($model, 'msg_flag') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
