<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\assets\GriddataAsset;
use yii\bootstrap\Modal;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Обращения';
$this->params['breadcrumbs'][] = $this->title;

GriddataAsset::register($this);

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
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'askid',
                'content' => function ($model, $key, $index, $column) {
                    return '№ ' . $model->msg_id . '<span>' . date('d.m.Y H:i:s', strtotime($model->msg_createtime)) . '</span>';
                },
                'contentOptions' => [
                    'class' => 'griddate',
                ],
            ],
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'asker',
                'content' => function ($model, $key, $index, $column) {
                    return Html::encode($model->msg_pers_lastname . ' ' . $model->msg_pers_name . ' ' . $model->msg_pers_secname );
                },
            ],
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'askcontacts',
                'content' => function ($model, $key, $index, $column) {
                    return Html::encode($model->msg_pers_email) . '<span>' . $model->msg_pers_phone . '</span>';
                },
                'contentOptions' => [
                    'class' => 'griddate',
                ],
            ],
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'tags',
                'content' => function ($model, $key, $index, $column) {
                    return Html::encode($model->region->reg_name) . '<span>' . Html::encode($model->msg_oldcomment) . '</span>';
                },
                'contentOptions' => [
                    'class' => 'griddate',
                ],
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

            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['class' => 'commandcell'],

                /*
                                'template'=>'{view} {delete}',
                                'buttons'=>[
                                    'view'=>function ($url, $model) {
                                        return Html::a( '<span class="glyphicon glyphicon-eye-open"></span>', $url,
                                            ['title' => 'Обращение № ' . $model->msg_id, 'class'=>'showinmodal']); // , 'data-pjax' => '0'
                //                            ['title' => Yii::t('yii', 'View'), 'class'=>'showinmodal']); // , 'data-pjax' => '0'
                                    }
                                ],
                */
            ],
        ],
    ]); ?>
    <?php
        // Окно для обращения
    Modal::begin([
        'header' => 'Обращение',
        'id' => 'messagedata',
    ]);
    Modal::end();

        $sJs =  <<<EOT
console.log("Shownmodal: " + jQuery('.showinmodal').length);
jQuery('.showinmodal').on("click", function (event){
    event.preventDefault();

    jQuery('#messagedata')
        .modal('show')
        .find('#modalContent')
        .load($(this).attr('href'));
    return false;
});
EOT;
        $this->registerJs($sJs, View::POS_READY, 'showmodalmessage');
    ?>

</div>
