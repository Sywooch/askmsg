<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SubjectTreeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="subject-tree-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'subj_id') ?>

    <?= $form->field($model, 'subj_created') ?>

    <?= $form->field($model, 'subj_variant') ?>

    <?= $form->field($model, 'subj_info') ?>

    <?= $form->field($model, 'subj_final_question') ?>

    <?php // echo $form->field($model, 'subj_final_person') ?>

    <?php // echo $form->field($model, 'subj_lft') ?>

    <?php // echo $form->field($model, 'subj_rgt') ?>

    <?php // echo $form->field($model, 'subj_level') ?>

    <?php // echo $form->field($model, 'subj_parent_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
