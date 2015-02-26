<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Messages';
$this->params['breadcrumbs'][] = $this->title;

/*
     <p>
        <?= Html::a('Create Message', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

*/
?>
<div class="message-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            // 'msg_id',
            // 'msg_createtime',
//            'msg_active',
            // 'msg_pers_name',
            // 'msg_pers_secname',
//            'msg_pers_lastname',
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'askid',
                'content' => function ($model, $key, $index, $column) {
                    return '№' . $model->msg_id . "<br />" . date('d.m.Y H:i:s', strtotime($model->msg_createtime));
                },
            ],
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'asker',
                'content' => function ($model, $key, $index, $column) {
                    return Html::encode($model->msg_pers_lastname . ' ' . $model->msg_pers_name . ' ' . $model->msg_pers_secname );
                },
            ],
            // 'msg_pers_email:email',
            // 'msg_pers_phone',
            // 'msg_pers_org',
            // 'msg_pers_region',
            // 'msg_pers_text:ntext',
/*
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'employer',
                'content' => function ($model, $key, $index, $column) {
                    return Html::encode($model->msg_empl_id . ($model->employee !== null ? (' ' . $model->employee->us_lastname . ' ' . $model->employee->us_name . ' ' . $model->employee->us_secondname) : '') );
                },
            ],
*/
            // 'msg_comment',
            // 'msg_empl_id',
            // 'msg_empl_command',
            // 'msg_empl_remark',
            // 'msg_answer:ntext',
            // 'msg_answertime',
            // 'msg_oldcomment',
            // 'msg_flag',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
