<?php

use yii\helpers\Html;
use app\models\Msgflags;
use app\models\Rolesimport;

/* @var $this yii\web\View */
/* @var $model app\models\Message */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="listdata">
    <div class="listregion">
        <?= $model->region->reg_name ?>
    </div>
    <div class="listdate">
        <?= date('d.m.Y H:i:s', strtotime($model->msg_createtime)) ?>
    </div>


    <div class="listperson">
        <!-- strong><?= Html::encode($model->msg_pers_lastname) ?></strong> <?= Html::encode($model->msg_pers_name) ?> <?= Html::encode($model->msg_pers_secname) ?> -->
        <strong><?= Html::encode($model->getFullName()) ?></strong>
    </div>

    <div class="listtext">
        <?= str_replace("\n", "<br />\n", $model->msg_pers_text) ?>
    </div>

    <?php if( !empty($model->msg_empl_command)  ): ?>
        <div class="listcommand">
            <strong>Поручение: </strong>
            <?= Html::encode($model->msg_empl_command) ?>
        </div>
    <?php endif; ?>

    <?php if( !empty($model->msg_empl_id)  ): ?>
        <div class="listemploee">
            <strong>Ответчик: </strong>
            <?= Html::encode($model->employee->getFullName()) ?>
            <span>
                <?= Html::encode($model->employee->us_workposition) ?>
            </span>
        </div>
    <?php endif; ?>

    <?php if( !empty($model->msg_answer)
           && (($model->msg_flag == Msgflags::MFLG_SHOW_ANSWER) || Yii::$app->user->can(Rolesimport::ROLE_MODERATE_DOGM))  ): ?>
        <div class="listemploee">
            <strong>Ответ: </strong>
            <?= $model->msg_answer ?>
        </div>
    <?php endif; ?>

</div>
