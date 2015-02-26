<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'us_xtime')->textInput() ?>

    <?= $form->field($model, 'us_login')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'us_password_hash')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'us_chekword_hash')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'us_active')->textInput() ?>

    <?= $form->field($model, 'us_name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'us_secondname')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'us_lastname')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'us_email')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'us_logintime')->textInput() ?>

    <?= $form->field($model, 'us_regtime')->textInput() ?>

    <?= $form->field($model, 'us_workposition')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'us_checkwordtime')->textInput() ?>

    <?= $form->field($model, 'auth_key')->textInput(['maxlength' => 32]) ?>

    <?= $form->field($model, 'email_confirm_token')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'password_reset_token')->textInput(['maxlength' => 255]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
