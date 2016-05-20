<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

use app\assets\GriddataAsset;
use app\assets\ListdataAsset;
use app\models\Rolesimport;
use app\models\Regions;
use app\models\Message;
use app\models\Msgflags;
use app\models\Tags;
use app\models\SendmsgForm;

use kartik\export\ExportMenu;
use app\models\Notificateact;

// use kartik\select2\Select2Asset;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model app\models\Message */
$this->title = 'Обращения';
$this->params['breadcrumbs'][] = $this->title;

GriddataAsset::register($this);
ListdataAsset::register($this);
// Select2Asset::register($this);

$this->registerCssFile('/themes/font-awesome-4.3.0/css/font-awesome.min.css', ['rel'=>"stylesheet"], 'font-awesome');

/*
     <h1><?= Html::encode($this->title) ?></h1>
*/

$aTags = ArrayHelper::map(Tags::getTagslist(Tags::TAGTYPE_TAG), 'tag_id', 'tag_title');
$sGridId = 'natificateMessageList';

//$sJs = <<<EOT
//jQuery("#сlearnotifylog").on(
//    "click",
//    function(event){
//        event.preventDefault();
//        var oLink = jQuery(this),
//            sAdr = oLink.attr("href");
//        jQuery.ajax({
//            type: "POST",
//            url: sAdr,
//            data: {},
//            success: function(data, textStatus, jqXHR ){
//                oLink.text("Очищено в логе " + data["clear"] + " записей");
//                oLink.attr("href", "#");
//            },
//            error: function(jqXHR, textStatus, errorThrown ) {
//                oLink.text("Ошибка очистки " + textStatus);
//                oLink.attr("href", "#");
//            },
//            dataType: "json"
//        });
//
//        return false;
//    }
//);
//EOT;
//$this->registerJs($sJs, View::POS_READY, 'сlearnotifylog');

$sFormName = $searchModel->formName();
$aExportParam = [
    'title' => 'Экспорт просроченных сообщений',
    'url' => Url::to(
        array_merge(
            ['exportnotify'],
            ['format' => 'xlsx', ]
//            $searchModel->getSearchArray()
        )
    ),
//    'icon' => 'fa-file-excel-o',
];


?>
<p>
    <?= Html::a('Провести действия', ['notificateact/send'], ['class' => 'btn btn-success', 'id' => 'doprocess', ]) ?>
    <?= Html::a(
        '<i class="fa fa-file-excel-o fa-fw"></i> Экспорт в Excel',
        Url::to(
            array_merge(
                ['exportnotify'],
                ['format' => 'xlsx', ]
            )
        ),
        ['class' => 'btn btn-success', 'id' => 'doprocess', ]
    ) ?>
    <?= '' // Html::a('Очистить лог', ['notificateact/clearnotifylog'], ['class' => 'btn btn-success', 'id' => 'сlearnotifylog', ]) ?>
</p>

<div class="message-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'id' => $sGridId,
        'filterModel' => $searchModel,
        'filterRowOptions' => ['style' => 'display: none;'],
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => 'yii\grid\CheckboxColumn',
            ],

            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'msg_id',
                'header' => 'Действия',
                'filter' => false,
                'content' => function ($model, $key, $index, $column) {
                    /** @var Message $model */
                    $days = Notificateact::getAdge($model->msg_createtime); // intval(($tToday - strtotime($model->msg_createtime)) / $n24, 10);
                    return nl2br("{$days} дней\n" . Html::encode(implode("\n", Notificateact::getDateAct($model->msg_createtime))));
                },
                'contentOptions' => [
                    'class' => 'griddate',
                ],
            ],

            [
                'class' => 'yii\grid\DataColumn',
//                'attribute' => 'msg_id',
                'attribute' => 'askid',
                'header' => 'Номер и дата',
                'filter' => false,
                'filterOptions' => ['class' => 'gridwidth7'],
                'content' => function ($model, $key, $index, $column) {
                    $url = Yii::$app->user->can(Rolesimport::ROLE_MODERATE_DOGM) ?
                        ['message/update', 'id'=>$model->msg_id] :
                        ['message/answer', 'id'=>$model->msg_id];
// ['title' => 'Изменить Обращение ' . $model->msg_id])
//                    update} {answer
                    return Html::a('№ ' . $model->msg_id, $url) . '<span>' . date('d.m.Y H:i', strtotime($model->msg_createtime)) . '</span>';
                },
                'contentOptions' => [
                    'class' => 'griddate',
                ],
            ],
            [
                'class' => 'yii\grid\DataColumn',
                'header' => 'Состояние',
                'attribute' => 'msg_flag',
                'filter' => false,
//                'filter' => ArrayHelper::map(Msgflags::getStateData(), 'fl_id', 'fl_sname'),
                'filterOptions' => ['class' => 'gridwidth7'],
                'content' => function ($model, $key, $index, $column) {
                    $sMark = '';
                    if( $model->msg_mark !== null ) {
                        $sColor = '#ee2222';
                        $sGlith = 'remove-sign';
                        if( $model->msg_mark == 5 ) {
                            $sColor = '#2e8b57';
                            $sGlith = 'ok-sign';
                        }
                        $sMark = '<a href="#" class="pull-right" data-toggle="tooltip" data-placement="top" title="Проситель '.( $model->msg_mark != 5 ? 'не ' : '' ).'удовлетворен ответом">'
                               . '<span class="glyphicon glyphicon-'
                               . $sGlith
                               . '" style="color: '
                               . $sColor
                               . '; font-size: 1.25em;"></span></a>';
                    }
                    else {
                        $sMark = Html::a('.', $model->getMarkUrl(), ['style'=>'display: none;']);
                    }
                    return $sMark . '<span class="glyphicon glyphicon-'.$model->flag->fl_glyth.'" style=" margin-right: 1.25em; color: '.$model->flag->fl_glyth_color.'; font-size: 1.25em;"></span>' //  font-size: 1.25em;
                    . '<span class="inline">' . $model->flag->fl_sname . '</span>'; //  . ' ' . $model->msg_flag
                },
                'contentOptions' => [
                    'class' => 'griddate',
                ],
            ],
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'msg_pers_lastname',
                'header' => 'Проситель',
                'filter' => false,
                'content' => function ($model, $key, $index, $column) {
                    $sEmpl = '';
                    if( $model->msg_empl_id !== null ) {
                        $sEmpl = Html::encode($model->employee->getFullName())/*
                               . ' '
                               . Html::a(
                                    '<span class="glyphicon glyphicon-search inlineblock"></span>',
                                    '?MessageSearch[msg_empl_id]=' . $model->msg_empl_id . Msgflags::makeSearchString('MessageSearch[msg_flag]'),
                                    ['title'=>'Поиск ответов исполнителя']
                               )*/;
                    }
                    return Html::encode($model->getFullName())
/*                        . ' '
                        . Html::a(
                            '<span class="glyphicon glyphicon-search inlineblock"></span>',
                            '?MessageSearch[msg_pers_lastname]='
                                . rawurlencode($model->getFullName())
                                . Msgflags::makeSearchString('MessageSearch[msg_flag]'),
                            ['title'=>'Поиск вопросов просителя']
                        )*/
                        . '<span>'
                        . $sEmpl
                        . '</span>';
                },
                'contentOptions' => [
                    'class' => 'griddate commandcell',
                ],
            ],
/*
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'askcontacts',
                'filter' => false,
                'content' => function ($model, $key, $index, $column) {
                    return Html::encode($model->msg_pers_email) . '<span>' . $model->msg_pers_phone . '</span>';
                },
                'contentOptions' => [
                    'class' => 'griddate',
                ],
            ],
*/
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'msg_empl_command',
                'header' => 'Поручение',
                'filter' => false,
                'content' => function ($model, $key, $index, $column) {
                    return Html::encode($model->msg_empl_command) . '<span>' . Html::encode($model->msg_empl_remark) . '</span>';
                },
                'contentOptions' => [
                    'class' => 'griddate',
                ],
            ],

/*
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'alltags',
//                'filter' => $aTags,
                'filter' => false,
                'filterOptions' => ['class' => 'gridwidth7'],
                'content' => function ($model, $key, $index, $column) {
                    return Html::encode(implode(', ', ArrayHelper::map($model->alltags, 'tag_id', 'tag_title')));
                },
                'contentOptions' => [
                    'class' => 'griddate',
                ],
            ],
*/
            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['class' => 'commandcell'],
                'template'=>'{view}', //  {send} {update} {answer} {toword} {delete}
                'buttons'=>[
                    'view'=>function ($url, $model) {
                        return Html::a( '<span class="glyphicon glyphicon-eye-open"></span>', ['message/view', 'id'=>$model->msg_id],
                            ['title' => 'Обращение № ' . $model->msg_id, 'class'=>'showinmodal']); // , 'data-pjax' => '0'
//                            ['title' => Yii::t('yii', 'View'), 'class'=>'showinmodal']); // , 'data-pjax' => '0'
                    },
                    'update'=>function ($url, $model) {
                        return Yii::$app->user->can(Rolesimport::ROLE_MODERATE_DOGM) ?
                            Html::a( '<span class="glyphicon glyphicon-pencil"></span>', $url, ['title' => 'Изменить обращение ' . $model->msg_id]) :
                            '';
                   },
                    'answer'=>function ($url, $model) {
                        return $model->isAnswerble ?
                            Html::a( '<span class="glyphicon glyphicon-refresh"></span>', $url, ['title' => 'Ответить на обращение ' . $model->msg_id]) :
                            '';
                    },
                    'toword'=>function ($url, $model) {
                        return Html::a( '<span class="glyphicon glyphicon-floppy-disk"></span>', $url, ['title' => 'Экспорт в Word', 'target' => '_blank']);
                    },
                    'send'=>function ($url, $model) {
                        return Html::a( '<span class="glyphicon glyphicon-envelope"></span>', $url, ['title' => 'Отправить по почте', 'target' => '_blank', 'class'=>'showmail']);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Yii::$app->user->can(Rolesimport::ROLE_MODERATE_DOGM) ?
                            Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                'title' => Yii::t('yii', 'Delete'),
                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                'data-method' => 'post',
                                'data-pjax' => '0',
                            ]) :
                            '';
                    }
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

    oBody.text("");
    oBody.load(oLink.attr('href'), params);
    ob.find('.modal-header span').text(oLink.attr('title'));
    ob.modal('show');
//    jQuery(".modal-content").css({'max-height': jQuery('window').height() * 0.7 + 'px'})
    return false;
});

jQuery('.showmail').on("click", function (event){
    event.preventDefault();

    var ob = jQuery('#maildata'),
        oBody = ob.find('.modal-body'),
        oLink = $(this);

    oBody.text("");
    oBody.load(oLink.attr('href'), params);
    ob.find('.modal-header span').text(oLink.attr('title'));
    ob.modal('show');
//    jQuery(".modal-content").css({'max-height': jQuery('window').height() * 0.7 + 'px'})
    return false;
});

jQuery('[data-toggle="tooltip"]').tooltip();

var oSelAll = jQuery('.select-on-check-all');
if( !oSelAll.is(":checked") ) {
    oSelAll.trigger("click");
}

jQuery('#doprocess').on("click", function(event) {
    event.preventDefault();
    var keys = jQuery('#{$sGridId}').yiiGridView('getSelectedRows'),
        adr = jQuery(this).attr("href");
    console.log("keys = ", keys);

        if( keys.length > 0 ) {
            keys.reverse();
            var f = function(){
                if( keys.length > 0 ) {
                    var id = keys.pop();
//                    console.log("id = " + id);
                    jQuery.ajax({
                        url: adr,
                        data: {id: id},
                        dataType: "json",
                        method: "POST",
                        success: function(data, textStatus, jqXHR) {
//                            console.log("Success: ", data);
                            jQuery('input[type="checkbox"][value="'+id+'"]').trigger("click");
                            window.setTimeout(f, 150);
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log("Error: " + textStatus + " " + errorThrown);
                        }
                    });
                }
            };
            f();
        }
    return false;
});


EOT;
        $this->registerJs($sJs, View::POS_READY, 'showmodalmessage');
    ?>

</div>
