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
use app\models\Message;
use app\models\Msgflags;
use app\models\Tags;

use kartik\export\ExportMenu;

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

$aTags = ArrayHelper::map(Tags::getTagslist(Tags::TAGTYPE_TAG), 'tag_id', 'tag_title');

?>
<div class="message-index">

    <div class="col-sm-12">
        <div class="form-group">
            <?= Html::a('Скрыть', '#', ['class' => 'btn btn-default pull-right', 'id'=>'hidesearchpanel', 'role'=>"button"]) ?>
            <?php

            $bOnlyLoggedUser = ($action[0] == 'answerlist');

            if( Yii::$app->user->can(Rolesimport::ROLE_MODERATE_DOGM) ) {
            ?>
                <?= $this->render(
                    '_flagstat',
                    ['bOnlyLoggedUser' => false]
                )
                ?>
            <?php
            }
            elseif( Yii::$app->user->can(Rolesimport::ROLE_ANSWER_DOGM) ) {
                ?>
                <?= $this->render(
                    '_flagstat',
                    ['bOnlyLoggedUser' => $bOnlyLoggedUser]
                )
                ?>
            <?php
            }
            ?>
            <div class="clearfix"></div>
        </div>
    </div>


    <?php
        $idserchblock = 'idsearchpanel';
        echo $this->render(
            '_search',
            [
                'model' => $searchModel,
                'action' => $action,
                'idserchblock' => $idserchblock,
            ]
        );

// показ/скрытие формы фильтрации
    $sJs =  <<<EOT
var oPanel = jQuery("#{$idserchblock}"),
    oLink = jQuery("#hidesearchpanel"),
    renameButton = function() {
        oLink.text((oPanel.is(":visible") ? "Скрыть" : "Показать") + " форму поиска");
    },
    toggleSearchPanel = function() {
        oPanel.toggle();
        renameButton();
    };

renameButton();
oLink.on(
    "click",
    function(event){ event.preventDefault(); toggleSearchPanel(); return false; }
);

EOT;
    $this->registerJs($sJs, View::POS_READY , 'togglesearchpanel');
    $exportDataProvider = clone($dataProvider);

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
                    $url = Yii::$app->user->can(Rolesimport::ROLE_MODERATE_DOGM) ?
                        ['message/update', 'id'=>$model->msg_id] :
                        ['message/answer', 'id'=>$model->msg_id];
// ['title' => 'Изменить Обращение ' . $model->msg_id])
//                    update} {answer
                    return Html::a('№ ' . $model->msg_id, $url) . '<span>' . date('d.m.Y H:i:s', strtotime($model->msg_createtime)) . '</span>';
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
                'attribute' => 'alltags',
                'filter' => $aTags,
                'filterOptions' => ['class' => 'gridwidth7'],
                'content' => function ($model, $key, $index, $column) {
                    return Html::encode(implode(', ', ArrayHelper::map($model->alltags, 'tag_id', 'tag_title')));
                },
                'contentOptions' => [
                    'class' => 'griddate',
                ],
            ],
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
                        return $model->isAnswerble ?
                            Html::a( '<span class="glyphicon glyphicon-refresh"></span>', $url, ['title' => 'Изменить Обращение ' . $model->msg_id]) :
                            '';
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

    oBody.load(oLink.attr('href'), params);
    ob.find('.modal-header span').text(oLink.attr('title'));
    ob.modal('show');
//    jQuery(".modal-content").css({'max-height': jQuery('window').height() * 0.7 + 'px'})
    return false;
});


EOT;
        $this->registerJs($sJs, View::POS_READY, 'showmodalmessage');
    ?>
    <div class="col-sm-12">
        <div class="form-group">
            <label class="control-label">Экспорт данных </label>

            <?= ExportMenu::widget([
                'dataProvider' => $exportDataProvider,
                'filename' => 'user-messages',
                'columns' => [
                    'msg_id',
                    [
                        'class' => 'yii\grid\DataColumn',
                        'attribute' => 'msg_createtime',
                        'content' => function ($model, $key, $index, $column) {
                            return date('d.m.Y H:i:s', strtotime($model->msg_createtime));
                        },

                    ],
                    [
                        'class' => 'yii\grid\DataColumn',
                        'header' => 'Состояние',
                        'attribute' => 'msg_flag',
                        'content' => function ($model, $key, $index, $column) {
                            return $model->flag->fl_sname;
                        },
                    ],
                    [
                        'class' => 'yii\grid\DataColumn',
                        'header' => 'Состояние',
                        'attribute' => 'flag.fl_sname',
                    ],
                    [
                        'class' => 'yii\grid\DataColumn',
                        'attribute' => 'msg_pers_lastname',
                        'header' => 'Проситель',
                        'content' => function ($model, $key, $index, $column) {
                            /** @var Message $model */
                            return $model->getFullName();
                        },
                    ],
                    'msg_pers_email',
                    'msg_pers_phone',
                    [
                        'class' => 'yii\grid\DataColumn',
                        'attribute' => 'msg_empl_id',
                        'content' => function ($model, $key, $index, $column) {
                            /** @var Message $model */
                            return (($model->msg_empl_id !== null) ? $model->employee->getFullName() : '');
                        },
                    ],
                    [
                        'attribute' => 'msg_pers_org',
                        'content' => function ($model, $key, $index, $column) {
                            /** @var Message $model */
                            return htmlspecialchars_decode($model->msg_pers_org);
                        },
                    ],
                    [
                        'attribute' => 'msg_pers_text',
                        'content' => function ($model, $key, $index, $column) {
                            /** @var Message $model */
                            return strip_tags(htmlspecialchars_decode($model->msg_pers_text));
                        },
                    ],
                    'msg_empl_command',
                    [
                        'attribute' => 'msg_answer',
                        'content' => function ($model, $key, $index, $column) {
                            /** @var Message $model */
                            return htmlspecialchars_decode(strip_tags($model->msg_answer));
                        },
                    ],
                    'msg_empl_remark',
                ]
            ]);  ?>
        </div>
    </div>

</div>
