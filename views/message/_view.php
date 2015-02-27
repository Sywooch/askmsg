<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Message */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="listdata">
    <div class="listdate">
        <?= date('d.m.Y H:i:s', strtotime($model->msg_createtime)) ?>
        <?= $model->region->reg_name ?>
    </div>


    <div class="listperson">
        <strong><?= Html::encode($model->msg_pers_lastname) ?></strong> <?= Html::encode($model->msg_pers_name) ?> <?= Html::encode($model->msg_pers_secname) ?>
    </div>

    <div class="listtext">
        <?= str_replace("\n", "<br />\n", $model->msg_pers_text) ?>
    </div>

    <?php if( !empty($model->msg_empl_command)  ): ?>
        <div class="listcommand">
            <?= Html::encode($model->msg_empl_command) ?>
        </div>
    <?php endif; ?>

    <?php if( !empty($model->msg_empl_id)  ): ?>
        <div class="listemploee">
            <?= Html::encode($model->employee->us_lastname) ?>
            <?= Html::encode($model->employee->us_name) ?>
            <?= Html::encode($model->employee->us_secondname) ?>
            <span>
                <?= Html::encode($model->employee->us_workposition) ?>
            </span>
        </div>
    <?php endif; ?>

    <?php if( !empty($model->msg_answer)  ): ?>
        <div class="listemploee">
            <?= $model->msg_answer ?>
        </div>
    <?php endif; ?>

</div>
