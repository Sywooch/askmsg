<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\assets\GriddataAsset;
use app\models\Tags;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TagsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Теги';
$this->params['breadcrumbs'][] = $this->title;

GriddataAsset::register($this);

/* <h1><?= Html::encode($this->title) ?></h1> */

?>
<div class="tags-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить тег', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Добавить тему', ['create', 'type'=>Tags::TAGTYPE_SUBJECT], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
            ],

//            'tag_id',
            [
                'class' => 'yii\grid\DataColumn',
                'filter' => Tags::$_aTypes,
//                'filterOptions' => ['class' => 'gridwidth7'],
                'attribute' => 'tag_type',
                'content' => function ($model, $key, $index, $column) {
                    /** @var $model Tags  */
                    return Html::encode($model->getTypename());
                },
            ],
            [
                'class' => 'yii\grid\DataColumn',
                'filter' => ['Нет', 'Да'],
                'filterOptions' => ['class' => 'gridwidth7'],
                'attribute' => 'tag_active',
                'content' => function ($model, $key, $index, $column) {
                    return '<span class="glyphicon glyphicon-'.($model->tag_active ? 'ok' : 'remove').'" aria-hidden="true"></span>';
                },
            ],
            'tag_title',
            [
                'class' => 'yii\grid\DataColumn',
                'filter' => ['Нет', 'Да'],
                'attribute' => 'tag_rating_val',
                'content' => function ($model, $key, $index, $column) {
                    return $model->tag_rating_val ? '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>' : '';
                },
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['class' => 'commandcell'],
            ],
        ],
    ]); ?>

</div>
