<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SubjectTree */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="subject-tree-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'subj_info')->textarea(['rows' => 6]) ?>


    <div class="form-group">
        <?= Html::submitButton('Отправить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
