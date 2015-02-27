<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use app\assets\ListdataAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Обращения граждан';
$this->params['breadcrumbs'][] = $this->title;

ListdataAsset::register($this);

/*
     <p>
        <?= Html::a('Create Message', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

*/
?>
<div class="message-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_view',
        'layout' => "{items}\n{pager}",
    ]); ?>

</div>
