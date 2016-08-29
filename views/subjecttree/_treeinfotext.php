<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\SubjectTree */
/* @var $formmodel app\models\MessageTreeForm */
/* @var $form yii\widgets\ActiveForm */

if( ($model !== null) && !empty($model->subj_info) ) {
    $sMsg = Html::encode($model->subj_info);
    ?>

    <p style="margin-bottom:30px;"><?= $sMsg ?></p>

    <div class="row">
        <div class="col-sm-4 col-sm-offset-4">
            Была ли данная информация Вам полезной?
        </div>
    </div>
    <div class="row">
        <div class="col-sm-2 col-sm-offset-4">
            <?= $form->field($formmodel, 'is_satisfied', ['template' => '{input}'])->radioList([1 => 'Да', 2 => 'Нет',], ['separator' => ' ',]) ?>
        </div>
    </div>

<?php
}
