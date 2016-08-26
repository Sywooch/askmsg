<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SubjectTree */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="subject-tree-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'subj_created')->textInput() ?>

    <?= $form->field($model, 'subj_variant')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'subj_info')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'subj_final_question')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'subj_final_person')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'subj_lft')->textInput() ?>

    <?= $form->field($model, 'subj_rgt')->textInput() ?>

    <?= $form->field($model, 'subj_level')->textInput() ?>

    <?= $form->field($model, 'subj_parent_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
