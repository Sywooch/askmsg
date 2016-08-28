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
    $nSubjId = $model->subj_id;
//    <div style="border: solid #cccccc 1px; padding: 15px;"></div>
//    $model, 'is_satisfied'
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
            <?= Html::a('Да', ['subjecttree/newmsg', 'id' => $nSubjId, 'satisfy' => 1, ], ['class' => 'btn btn-success btn-block setsetisfited', 'data-satisfy' => 1, ]) ?>
        </div>
        <div class="col-sm-2">
            <?= Html::a('Нет', ['subjecttree/newmsg', 'id' => $nSubjId, 'satisfy' => 2, ], ['class' => 'btn btn-danger btn-block setsetisfited', 'data-satisfy' => 2, ]) ?>
        </div>
    </div>
</div>

<?php

    $sIdSatisfited = Html::getInputId($formmodel, 'is_satisfied');

    $sJs = <<<EOT
jQuery(".subject-tree-message-form").hide();

jQuery(".setsetisfited").on(
    "click",
    function(event) {
        event.preventDefault();
        var sVal = jQuery(this).data("satisfy");
        jQuery("#{$sIdSatisfited}").val(sVal);
        console.log("set val = " + sVal);
        jQuery(".subject-tree-message-form").show();
        return false;
    }
);
EOT;
    $this->registerJs($sJs, View::POS_READY);

}

