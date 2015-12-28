<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

use app\models\Subjredirect;
use app\models\Tags;
use app\assets\GriddataAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SubjredirectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Перенаправления по темам вопросов';
$this->params['breadcrumbs'][] = $this->title;

GriddataAsset::register($this);

?>
<div class="subjredirect-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

//            'redir_id',
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'redir_tag_id',
                'filter' => ArrayHelper::map(Tags::getTagslist(Tags::TAGTYPE_SUBJECT), 'tag_id', 'tag_title'),
                'format' => 'raw',
                'content' => function ($model, $key, $index, $column) {
                    /** @var Subjredirect $model */
                    return Html::encode($model->subject ? $model->subject->tag_title : '');
                },
            ],
//            'redir_tag_id',
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'redir_adress',
//                'filter' => ArrayHelper::map(Tags::getTagslist(Tags::TAGTYPE_SUBJECT), 'tag_id', 'tag_title'),
                'format' => 'raw',
                'content' => function ($model, $key, $index, $column) {
                    /** @var Subjredirect $model */
                    return Html::encode($model->redir_adress) . ($model->redir_description ? ('<span>' . Html::encode($model->redir_description) . '</span>') : '');
                },
                'contentOptions' => [
                    'class' => 'griddate',
                ],
            ],
//            'redir_adress',
//            'redir_description',

//            ['class' => 'yii\grid\ActionColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['class' => 'commandcell'],
                'template'=>'{view} {update} {delete}',
            ],
        ],
    ]); ?>

</div>
