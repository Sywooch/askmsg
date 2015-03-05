<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Msgtags */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="msgtags-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'mt_msg_id')->textInput() ?>

    <?= $form->field($model, 'mt_tag_id')->textInput(['maxlength' => 255]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
