<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UsergroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Usergroups';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="usergroup-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Usergroup', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'usgr_id',
            'usgr_uid',
            'usgr_gid',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
