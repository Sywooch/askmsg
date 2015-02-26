<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Message */

$this->title = $model->msg_id;
$this->params['breadcrumbs'][] = ['label' => 'Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->msg_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->msg_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'msg_id',
            'msg_createtime',
            'msg_active',
            'msg_pers_name',
            'msg_pers_secname',
            'msg_pers_lastname',
            'msg_pers_email:email',
            'msg_pers_phone',
            'msg_pers_org',
            'msg_pers_region',
            'msg_pers_text:ntext',
            'msg_comment',
            'msg_empl_id',
            'msg_empl_command',
            'msg_empl_remark',
            'msg_answer:ntext',
            'msg_answertime',
            'msg_oldcomment',
            'msg_flag',
        ],
    ]) ?>

</div>
