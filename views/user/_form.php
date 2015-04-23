<?php

use yii\helpers\Html;
// use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use app\models\Group;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
/*
<?= $form->field($model, 'us_xtime')->textInput() ?>
<?= $form->field($model, 'us_password_hash')->textInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'us_chekword_hash')->textInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'us_logintime')->textInput() ?>
<?= $form->field($model, 'us_regtime')->textInput() ?>
<?= $form->field($model, 'us_checkwordtime')->textInput() ?>
<?= $form->field($model, 'auth_key')->textInput(['maxlength' => 32]) ?>
<?= $form->field($model, 'email_confirm_token')->textInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'password_reset_token')->textInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'us_active')->textInput() ?>
*/
?>

<div class="user-form" style="margin-bottom: 3em;">

    <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'label' => 'col-sm-3',
                    'offset' => 'col-sm-offset-3',
                    'wrapper' => 'col-sm-6',
                    //                    'error' => '',
                    //                    'hint' => '',
                ],
            ],

        ]); ?>

    <?= $form->field($model, 'us_active')->checkbox() ?>

    <?= $form->field($model, 'us_lastname')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'us_name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'us_secondname')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'us_email')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'us_workposition')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'us_login')->textInput(['maxlength' => 255])->hint("необязательное поле, будет создан из email") ?>

    <?= $form->field($model, 'newPassword')->textInput(['maxlength' => 32])->hint("если указать, то будет установлен новый пароль") ?>

    <?= $form->field($model, 'selectedGroups')->checkboxList(Group::getActiveGroups()) ?>






            <div class="col-sm-6 col-sm-offset-3">
                <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Изменить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
    <div class="clearfix"></div>

    <?php ActiveForm::end(); ?>

</div>
