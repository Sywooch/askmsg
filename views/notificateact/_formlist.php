<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Notificateactl;

/* @var $this yii\web\View */
/* @var $model app\models\Notificateact */
/* @var $form yii\widgets\ActiveForm */

$sId = $index;
if( ($model->ntfd_flag & 1) > 0 ) {
    $model->ntfd_message_age .= '+';
}
?>

<div class="col-sm-1">
    <?= $form
        ->field($model, '[' . $sId . ']ntfd_message_age', ['template' => "{input}\n{hint}\n{error}"])
        ->textInput()
    //            ->widget(Select2::classname(), $aResource) ?>
</div>

<div class="col-sm-3">
    <?= $form
        ->field($model, '[' . $sId . ']ntfd_operate', ['template' => "{input}\n{hint}\n{error}"])
        ->dropDownList(
            $model->acts
//            [
//                'options' => $model->acts,
//            ]
        )
    //            ->widget(Select2::classname(), $aRole) ?>
</div>

<div class="col-sm-1">
    <?= Html::a(
        Html::tag('span', '', ['class' => 'glyphicon glyphicon-remove']),
        '',
        [
            'class' => 'btn btn-danger remove-action',
        ]
    ) ?>
</div>

<?= '' //$form->field($model, 'udat_res_id')->textInput() ?>
<div class="clearfix"></div>

