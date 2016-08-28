<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SubjectTree */
/* @var $formmodel app\models\MessageTreeForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="subject-tree-message-form">

    <?php $form = ActiveForm::begin([
        'action' => ['subjecttree/newmsg', 'id' => $model->subj_id],
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'validateOnChange' => false,
        'validateOnBlur' => false,
        'validateOnType' => false,
        'validateOnSubmit' => true,
    ]); ?>

    <?= $form->field($formmodel, 'msg_pers_text')->textarea(['rows' => 6]) ?>
    <?= $form->field($formmodel, 'is_satisfied', ['template' => '{input}', ])->hiddenInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Отправить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
