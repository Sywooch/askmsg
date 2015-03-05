<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MsgtagsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Msgtags';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="msgtags-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Msgtags', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'mt_id',
            'mt_msg_id',
            'mt_tag_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
