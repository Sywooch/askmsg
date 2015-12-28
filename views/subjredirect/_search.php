<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SubjredirectSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="subjredirect-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'redir_id') ?>

    <?= $form->field($model, 'redir_tag_id') ?>

    <?= $form->field($model, 'redir_adress') ?>

    <?= $form->field($model, 'redir_description') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
