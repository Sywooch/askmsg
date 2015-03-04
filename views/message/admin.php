<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\web\View;
use yii\helpers\ArrayHelper;

use app\assets\GriddataAsset;
use app\assets\ListdataAsset;
use app\models\Rolesimport;
use app\models\Regions;
use app\models\Msgflags;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model app\models\Message */
$this->title = 'Обращения';
$this->params['breadcrumbs'][] = $this->title;

GriddataAsset::register($this);
ListdataAsset::register($this);

/*
     <h1><?= Html::encode($this->title) ?></h1>
 */

?>
<div class="message-index">

    <?php
        // echo $this->render('_search', ['model' => $searchModel]);
//        $aFlags = Msgflags::getStateData();

    ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => 'yii\grid\DataColumn',
//                'attribute' => 'msg_id',
                'attribute' => 'askid',
                'header' => 'Номер и дата',
                'filterOptions' => ['class' => 'gridwidth7'],
                'content' => function ($model, $key, $index, $column) {
                    return Html::a('№ ' . $model->msg_id, ['message/view', 'id'=>$model->msg_id]) . '<span>' . date('d.m.Y H:i:s', strtotime($model->msg_createtime)) . '</span>';
                },
                'contentOptions' => [
                    'class' => 'griddate',
                ],
            ],
            [
                'class' => 'yii\grid\DataColumn',
                'header' => 'Состояние',
                'attribute' => 'msg_flag',
                'filter' => ArrayHelper::map(Msgflags::getStateData(), 'fl_id', 'fl_sname'),
                'filterOptions' => ['class' => 'gridwidth7'],
                'content' => function ($model, $key, $index, $column) {
                    return '<span class="glyphicon glyphicon-'.$model->flag->fl_glyth.'" style="color: '.$model->flag->fl_glyth_color.'; font-size: 1.25em;"></span>' //  font-size: 1.25em;
                    . '<span class="inline">' . $model->flag->fl_sname . '</span>';
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
                        . '<span>' // . ($model->msg_flag ? $model->flag->fl_name : '--')
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
                'filterOptions' => ['class' => 'gridwidth7'],

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
        'size' => Modal::SIZE_LARGE,
    ]);
    Modal::end();

        $sJs =  <<<EOT
var params = {};
params[$('meta[name=csrf-param]').prop('content')] = $('meta[name=csrf-token]').prop('content');

jQuery('.showinmodal').on("click", function (event){
    event.preventDefault();

    var ob = jQuery('#messagedata'),
        oBody = ob.find('.modal-body'),
        oLink = $(this);

    oBody.load(oLink.attr('href'), params);
    ob.find('.modal-header span').text(oLink.attr('title'));
    ob.modal('show');
//    jQuery(".modal-content").css({'max-height': jQuery('window').height() * 0.7 + 'px'})
    return false;
});


EOT;
        $this->registerJs($sJs, View::POS_READY, 'showmodalmessage');
    ?>

</div>
