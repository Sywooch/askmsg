<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\NotificatelogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="notificatelog-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'ntflg_id') ?>

    <?= $form->field($model, 'ntflg_msg_id') ?>

    <?= $form->field($model, 'ntflg_ntfd_id') ?>

    <?= $form->field($model, 'ntflg_notiftime') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
