<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SupportSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Supports';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="support-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Support', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'sup_id',
            'sup_createtime',
            'sup_message:ntext',
            'sup_empl_id',
            'sup_active',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
