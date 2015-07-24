<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrgsovetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Orgsovets';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orgsovet-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Orgsovet', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'orgsov_id',
            'orgsov_sovet_id',
            'orgsov_ekis_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
