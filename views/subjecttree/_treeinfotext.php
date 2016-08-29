<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\SubjectTree */
/* @var $formmodel app\models\MessageTreeForm */
/* @var $form yii\widgets\ActiveForm */
//  && !$formmodel->isNeedAskdirector($model)
if( $model !== null ) {
    $bSatisfy = $formmodel->isNeedSatisfy($model);
    $bAskDirector = $formmodel->isNeedAskdirector($model);

    $sMsg = Html::encode($model->subj_info);
?>
    <div style="display: <?= $bSatisfy ? 'block' : 'none' ?>">
        <p style="margin-bottom:30px;"><?= $sMsg ?></p>

        <div class="row">
            <div class="col-sm-4 col-sm-offset-4">
                Была ли данная информация Вам полезной?
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2 col-sm-offset-4">
                <?= $form
                    ->field($formmodel, 'is_satisfied', ['template' => '{input}'])
                    ->radioList(
                        [1 => 'Да', 2 => 'Нет',],
                        [
                            'separator' => ' ',
                            'itemOptions' => [
                                'class' => 'satisfyclass',
                            ],
                        ]) ?>
            </div>
        </div>
    </div>

    <div style="display: <?= ($bAskDirector && !$bSatisfy) ? 'block' : 'none' ?>">
        <?php
        $sMsg = Html::encode($model->subj_final_question);
        ?>
        <p style="margin-bottom:30px;"><?= $sMsg ?></p>

        <div class="row">
            <div class="col-sm-2 col-sm-offset-4">
                <?= $form
                    ->field($formmodel, 'is_ask_director', ['template' => '{input}'])
                    ->radioList(
                        [1 => 'Да', 2 => 'Нет',],
                        [
                            'separator' => ' ',
                            'itemOptions' => [
                                'class' => 'askdirclass',
                            ],
                        ]) ?>
            </div>
        </div>
    </div>
<?php
}
