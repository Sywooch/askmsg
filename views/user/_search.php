<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'us_id') ?>

    <?= $form->field($model, 'us_xtime') ?>

    <?= $form->field($model, 'us_login') ?>

    <?= $form->field($model, 'us_password_hash') ?>

    <?= $form->field($model, 'us_chekword_hash') ?>

    <?php // echo $form->field($model, 'us_active') ?>

    <?php // echo $form->field($model, 'us_name') ?>

    <?php // echo $form->field($model, 'us_secondname') ?>

    <?php // echo $form->field($model, 'us_lastname') ?>

    <?php // echo $form->field($model, 'us_email') ?>

    <?php // echo $form->field($model, 'us_logintime') ?>

    <?php // echo $form->field($model, 'us_regtime') ?>

    <?php // echo $form->field($model, 'us_workposition') ?>

    <?php // echo $form->field($model, 'us_checkwordtime') ?>

    <?php // echo $form->field($model, 'auth_key') ?>

    <?php // echo $form->field($model, 'email_confirm_token') ?>

    <?php // echo $form->field($model, 'password_reset_token') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
