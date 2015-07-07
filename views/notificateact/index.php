<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\assets\GriddataAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\models\NotificateactSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Действия с обращениями';
$this->params['breadcrumbs'][] = $this->title;

GriddataAsset::register($this);

// $this->title = 'Notificateacts';
// $this->params['breadcrumbs'][] = $this->title;
?>
<div class="notificateact-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Notificateact', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'ntfd_id',
            'ntfd_message_age',
            'ntfd_operate',
            'ntfd_flag',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
