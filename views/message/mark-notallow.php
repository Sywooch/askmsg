<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;
use app\models\Msgflags;

/* @var $this yii\web\View */
/* @var $model app\models\Message */

$this->title = 'Оценка ответа на обращение № ' . $model->msg_id . ' от ' . date('d.m.Y', strtotime($model->msg_createtime));
$this->params['breadcrumbs'] = [];
// $this->params['breadcrumbs'][] = ['label' => 'Обращения', 'url' => $url];
// $this->params['breadcrumbs'][] = $this->title;

?>
<div class="message-mark">
    <p>Здравствуйте, <?= $model->getShortName() ?>.</p>
    <p><strong>Ваше обращение № <?= $model->msg_id . ' от ' . date('d.m.Y', strtotime($model->msg_createtime)) ?> не может быть оценено</strong>.</p>
    <?php
        if( $model->msg_mark !== null ) {
    ?>
            <p>Оно было оценено ранее.</p>
    <?php
        }
        else if( ($model->msg_flag != Msgflags::MFLG_SHOW_ANSWER) && ($model->msg_flag != Msgflags::MFLG_INT_FIN_INSTR)) {
    ?>
            <p>Оно не прошло цикл подготовки ответа.</p>
    <?php
        }
    ?>


</div>
