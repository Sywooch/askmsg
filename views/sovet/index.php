<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SovetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sovets';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sovet-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Sovet', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'sovet_id',
            'sovet_title',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
