<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\NotificatelogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Notificatelogs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notificatelog-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Notificatelog', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'ntflg_id',
            'ntflg_msg_id',
            'ntflg_ntfd_id',
            'ntflg_notiftime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
