<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AppealSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Appeals';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="appeal-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Appeal', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'ap_id',
            'ap_created',
            'ap_next_act_date',
            'ap_pers_name',
            'ap_pers_secname',
            // 'ap_pers_lastname',
            // 'ap_pers_email:email',
            // 'ap_pers_phone',
            // 'ap_pers_org',
            // 'ap_pers_region',
            // 'ap_pers_text:ntext',
            // 'ap_empl_command:ntext',
            // 'ap_comment:ntext',
            // 'ap_subject',
            // 'ap_empl_id',
            // 'ap_curator_id',
            // 'ekis_id',
            // 'ap_state',
            // 'ap_ans_state',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
