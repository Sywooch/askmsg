<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Support */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="support-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sup_createtime')->textInput() ?>

    <?= $form->field($model, 'sup_message')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'sup_empl_id')->textInput() ?>

    <?= $form->field($model, 'sup_active')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
