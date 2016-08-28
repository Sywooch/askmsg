<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SubjectTree */
/* @var $form yii\widgets\ActiveForm */

if( ($model !== null) && !empty($model->subj_info) ) {
    $sMsg = Html::encode($model->subj_info);
//    <div style="border: solid #cccccc 1px; padding: 15px;"></div>

?>

<div class="subject-tree-form">
    <p style="margin-bottom:30px;"><?= $sMsg ?></p>
    <div class="row">
        <div class="col-sm-4 col-sm-offset-4">
            Была ли данная информация Вам полезной?
        </div>
    </div>
    <div class="row">
        <div class="col-sm-2 col-sm-offset-4">
            <?= Html::a('Да', '', ['class' => 'btn btn-success btn-block']) ?>
        </div>
        <div class="col-sm-2">
            <?= Html::a('Нет', '', ['class' => 'btn btn-danger btn-block']) ?>
        </div>
    </div>
</div>

<?php
}