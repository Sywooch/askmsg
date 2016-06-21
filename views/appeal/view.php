<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Appeal */

$this->title = $model->ap_id;
$this->params['breadcrumbs'][] = ['label' => 'Appeals', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="appeal-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->ap_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->ap_id], [
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
            'ap_id',
            'ap_created',
            'ap_next_act_date',
            'ap_pers_name',
            'ap_pers_secname',
            'ap_pers_lastname',
            'ap_pers_email:email',
            'ap_pers_phone',
            'ap_pers_org',
            'ap_pers_region',
            'ap_pers_text:ntext',
            'ap_empl_command:ntext',
            'ap_comment:ntext',
            'ap_subject',
            'ap_empl_id',
            'ap_curator_id',
            'ekis_id',
            'ap_state',
            'ap_ans_state',
        ],
    ]) ?>

</div>
