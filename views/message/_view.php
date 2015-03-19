<?php

use yii\helpers\Html;
use app\models\Msgflags;
use app\models\Rolesimport;

/* @var $this yii\web\View */
/* @var $model app\models\Message */
/* @var $form yii\widgets\ActiveForm */
/* @var $oSubj app\models\Tags */

$isModerate = Yii::$app->user->can(Rolesimport::ROLE_MODERATE_DOGM);
$isAnswer = Yii::$app->user->can(Rolesimport::ROLE_ANSWER_DOGM);
$isDopFields = $isModerate || $isAnswer;
$oSubj = $model->subject;
?>

<div class="listdata">
    <div class="listregion">
        <?= $model->region->reg_name ?>
    </div>
    <div class="listdate">
        <?= date('d.m.Y H:i:s', strtotime($model->msg_createtime)) ?>
        <?= '№' . $model->msg_id . ' ' ?>
    </div>


    <div class="listperson">
        <strong><?= Html::encode($model->getFullName()) ?></strong>
    </div>

    <?php
    if( $isDopFields ) :
    ?>
        <div class="listdate">
            <strong><?= Html::encode($model->msg_pers_org) ?></strong>
        </div>
        <?php
            if( $oSubj !== null ) :
        ?>
            <div class="listdate" style="clear: both;">
                <strong><?= Html::encode($oSubj->tag_title) ?></strong>
            </div>
        <?php
            endif;
        ?>
    <?php
    endif;
    ?>

    <div class="listtext">
        <?= str_replace("\n", "<br />\n", Html::encode($model->msg_pers_text)) ?>
    </div>

    <?php
    $aFiles = $model->getUserFiles(true);
    if( (count($aFiles) > 0) && !Yii::$app->user->isGuest ):
    ?>
        <div class="listcommand">
            <strong>Файлы: </strong>
            <?php foreach($aFiles As $oFile):
            /** @var File  $oFile */ ?>
                <?= Html::a(
                        $oFile->file_orig_name,
                        $oFile->getUrl()
                ) ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if( !empty($model->msg_empl_command)  ): ?>
        <div class="listcommand">
            <strong>Поручение: </strong>
            <?= Html::encode($model->msg_empl_command) ?>
        </div>
    <?php endif; ?>

    <?php if( !empty($model->msg_comment) && $isDopFields ): ?>
        <div class="listcommand">
            <strong>Комментарий: </strong>
            <?= Html::encode($model->msg_comment) ?>
        </div>
    <?php endif; ?>

    <?php if( !empty($model->msg_empl_remark) && $isDopFields ): ?>
        <div class="listcommand">
            <strong>Замечание: </strong>
            <?= Html::encode($model->msg_empl_remark) ?>
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
        <?php
        $aFiles = $model->getUserFiles(false);
        $nFilesExists = count($aFiles);
        if( $nFilesExists > 0 ):
            ?>
            <div class="listcommand">
                <strong>Файлы к ответу: </strong>
                <?php
                foreach($aFiles As $oFile):
                    /** @var File  $oFile */
                    ?>
                    <?= Html::a(
                        $oFile->file_orig_name,
                        $oFile->getUrl()
                    )
                    ?>
                <?php
                endforeach;
                ?>
            </div>
        <?php
        endif; // if( $nFilesExists > 0 ):
        ?>
    <?php endif; ?>

    <?php if( $isDopFields && (count($model->answers) > 0) ): ?>
        <div class="listemploee">
            <strong>Соответчики: </strong>
            <?php foreach( $model->answers As $ob ): ?>
                <br />
                <?= $ob->getFullName() ?>
                <span>
                    <?= Html::encode($ob->us_workposition) ?>
                </span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
