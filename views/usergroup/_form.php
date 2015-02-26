<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Usergroup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="usergroup-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'usgr_uid')->textInput() ?>

    <?= $form->field($model, 'usgr_gid')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
