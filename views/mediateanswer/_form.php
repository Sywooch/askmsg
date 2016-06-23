<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Mediateanswer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mediateanswer-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ma_created')->textInput() ?>

    <?= $form->field($model, 'ma_text')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'ma_remark')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'ma_msg_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
