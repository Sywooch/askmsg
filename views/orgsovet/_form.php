<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Orgsovet */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="orgsovet-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'orgsov_sovet_id')->textInput() ?>

    <?= $form->field($model, 'orgsov_ekis_id')->textInput(['maxlength' => 20]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
