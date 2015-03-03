<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\web\View;

use app\assets\GriddataAsset;
use app\assets\ListdataAsset;
use app\models\Rolesimport;
use app\models\Regions;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model app\models\Message */
$this->title = 'Обращения';
$this->params['breadcrumbs'][] = $this->title;

GriddataAsset::register($this);
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


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'msg_id',
                'header' => 'Номер и дата',
                'content' => function ($model, $key, $index, $column) {
                    return '№ ' . $model->msg_id . '<span>' . date('d.m.Y H:i:s', strtotime($model->msg_createtime)) . '</span>';
                },
                'contentOptions' => [
                    'class' => 'griddate',
                ],
            ],
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'msg_pers_lastname',
                'header' => 'Проситель',
                'content' => function ($model, $key, $index, $column) {
                    return Html::encode($model->msg_pers_lastname . ' ' . $model->msg_pers_name . ' ' . $model->msg_pers_secname )
                        . '<span>' . ($model->msg_flag ? $model->flag->fl_name : '--')
                        . (($model->msg_empl_id !== null) ? Html::encode(' ' . $model->employee->getFullName()) : '')
                        . '</span>';
                },
                'contentOptions' => [
                    'class' => 'griddate',
                ],
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
                'attribute' => 'msg_pers_region',
//                'header' => '',
                'filter' => Regions::getListData(),
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

                'template'=>'{view} {update} {answer} {delete}',
                'buttons'=>[
                    'view'=>function ($url, $model) {
                        return Html::a( '<span class="glyphicon glyphicon-eye-open"></span>', $url,
                            ['title' => 'Обращение № ' . $model->msg_id, 'class'=>'showinmodal']); // , 'data-pjax' => '0'
//                            ['title' => Yii::t('yii', 'View'), 'class'=>'showinmodal']); // , 'data-pjax' => '0'
                    },
                    'update'=>function ($url, $model) {
                        return Yii::$app->user->can(Rolesimport::ROLE_MODERATE_DOGM) ?
                            Html::a( '<span class="glyphicon glyphicon-pencil"></span>', $url, ['title' => 'Изменить Обращение ' . $model->msg_id]) :
                            '';
                   },
                    'answer'=>function ($url, $model) {
                        return Yii::$app->user->can(Rolesimport::ROLE_ANSWER_DOGM) ?
                            Html::a( '<span class="glyphicon glyphicon-refresh"></span>', $url, ['title' => 'Изменить Обращение ' . $model->msg_id]) :
                            '';
/*                        return Html::a( '<span class="glyphicon glyphicon-refresh"></span>', $url,
                            ['title' => 'Изменить Обращение ' . $model->msg_id]);
*/
                    },
                ],

            ],
        ],
    ]); ?>
    <?php
        // Окно для обращения
    Modal::begin([
        'header' => '<span></span>',
        'id' => 'messagedata',
    ]);
    Modal::end();

        $sJs =  <<<EOT
var params = {};
params[$('meta[name=csrf-param]').prop('content')] = $('meta[name=csrf-token]').prop('content');

jQuery('.showinmodal').on("click", function (event){
    event.preventDefault();

    var ob = jQuery('#messagedata'),
        oLink = $(this);

    ob.find('.modal-body').load(oLink.attr('href'), params);
    ob.find('.modal-header span').text(oLink.attr('title'));
    ob.modal('show');
    return false;
});
EOT;
        $this->registerJs($sJs, View::POS_READY, 'showmodalmessage');
    ?>

</div>
