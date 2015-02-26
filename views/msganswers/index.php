<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MsganswersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Msganswers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="msganswers-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Msganswers', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'ma_id',
            'ma_message_id',
            'ma_user_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
