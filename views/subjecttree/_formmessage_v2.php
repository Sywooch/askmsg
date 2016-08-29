<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SubjectTree */
/* @var $formmodel app\models\MessageTreeForm */
/* @var $form yii\widgets\ActiveForm */
/* @var $step integer */
/* @var $subjectid integer */
Yii::info(__FILE__);
?>

<div class="subject-tree-message-form">
    <?= '' // 'step = ' . $step ?>

    <?php $form = ActiveForm::begin([
//        'action' => ['subjecttree/stepmasg', 'id' => $model->subj_id],
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'validateOnChange' => false,
        'validateOnBlur' => false,
        'validateOnType' => false,
        'validateOnSubmit' => true,
    ]); ?>

    <?= Html::hiddenInput('step', $step) ?>

    <div class="step_1" style="display: <?= ($step == 1) ? 'block' : 'none' ?>;">
        <div class="row">
            <div class="col-sm-3"><?= $form->field($formmodel, 'msg_pers_name')->textInput() ?></div>
            <div class="col-sm-3"><?= $form->field($formmodel, 'msg_pers_secname')->textInput() ?></div>
            <div class="col-sm-3"><?= $form->field($formmodel, 'msg_pers_lastname')->textInput() ?></div>
        </div>

        <div class="row">
            <div class="col-sm-3"><?= $form->field($formmodel, 'msg_pers_email')->textInput() ?></div>
            <div class="col-sm-3"><?= $form->field($formmodel, 'msg_pers_phone')->textInput() ?></div>
        </div>
    </div>

    <div class="step_2" style="display: <?= ($step == 2) ? 'block' : 'none' ?>;">
        <?= '' // 'subjectid = ' . $subjectid ?>
        <?= $this->render(
            'treeform',
            [
                'form' => $form,
                'formmodel' => $formmodel,
                'model' => $model,
                'child' => ($step == 2) ? $child : [],
                'parents' => ($step == 2) ? $parents : [],
            ]
        ) ?>
        <?= '' // $form->field($formmodel, 'subject_id', ['template' => '{input}', ])->hiddenInput() ?>
        <?= '' // $form->field($formmodel, 'is_satisfied', ['template' => '{input}', ])->hiddenInput() ?>
    </div>

    <div class="step_3" style="display: <?= ($step == 3) ? 'block' : 'none' ?>;">
        <?= $form->field($formmodel, 'msg_pers_text')->textarea(['rows' => 6]) ?>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-sm-2" style="display: none;<?= '' // ($step > 1) ? 'block' : 'none' ?>;"><?= Html::submitButton('Назад', ['class' => 'btn btn-success', 'name' =>'prev', ]) ?></div>
            <div class="col-sm-2"><?= Html::submitButton(($step < 3) ? 'Далее' : 'Отправить', ['class' => 'btn btn-success', 'name' =>'next', ]) ?></div>
        </div>

    </div>

    <?php ActiveForm::end(); ?>

</div>
