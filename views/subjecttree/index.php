<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SubjectTreeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Subject Trees';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subject-tree-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Subject Tree', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'subj_id',
            'subj_created',
            'subj_variant:ntext',
            'subj_info:ntext',
            'subj_final_question:ntext',
            // 'subj_final_person:ntext',
            // 'subj_lft',
            // 'subj_rgt',
            // 'subj_level',
            // 'subj_parent_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
