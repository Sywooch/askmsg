<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MediateanswerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mediateanswers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mediateanswer-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Mediateanswer', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'ma_id',
            'ma_created',
            'ma_text:ntext',
            'ma_remark:ntext',
            'ma_msg_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
