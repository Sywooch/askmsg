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
use app\components\Urllocation;
use app\models\SendmsgForm;

use kartik\export\ExportMenu;

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

?>
<div class="message-index">

    <!-- div class="col-sm-12" -->
        <div class="form-group">
            <?= Html::a('Скрыть', '#', ['class' => 'btn btn-default pull-right', 'id'=>'hidesearchpanel', 'role'=>"button", 'style'=>'margin-right: 0;']) ?>
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
    <!-- /div -->


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
//        oLink.text((oPanel.is(":visible") ? "Скрыть" : "Показать") + " форму поиска");
        oLink.text(/*(oPanel.is(":visible") ? "-" : "+") + */"поиск");
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
        'filterRowOptions' => ['style' => 'display: none;'],
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
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
                        $sColor = '#ff0000';
                        $sGlith = 'remove-sign';
                        if( $model->msg_mark == 5 ) {
                            $sColor = '#00cc00';
                            $sGlith = 'ok-sign';
                        }
                        $sMark = '<a href="#" class="pull-right" data-toggle="tooltip" data-placement="top" title="Проситель '.( $model->msg_mark != 5 ? 'не ' : '' ).'удвлетворен ответом">'
                               . '<span class="glyphicon glyphicon-'
                               . $sGlith
                               . '" style="color: '
                               . $sColor
                               . '; font-size: 1.25em;"></span></a>';
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
                        $sEmpl = Html::encode($model->employee->getFullName())
                               . ' '
                               . Html::a(
                                    '<span class="glyphicon glyphicon-search inlineblock"></span>',
                                    '?MessageSearch[msg_empl_id]=' . $model->msg_empl_id . Msgflags::makeSearchString('MessageSearch[msg_flag]'),
                                    ['title'=>'Поиск ответов исполнителя']
                               );
                    }
                    return Html::encode($model->getFullName())
                        . ' '
                        . Html::a(
                            '<span class="glyphicon glyphicon-search inlineblock"></span>',
                            '?MessageSearch[msg_pers_lastname]='
                                . rawurlencode($model->getFullName())
                                . Msgflags::makeSearchString('MessageSearch[msg_flag]'),
                            ['title'=>'Поиск вопросов просителя']
                        )
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
            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['class' => 'commandcell'],
                'template'=>'{view} {update} {answer} {toword} {delete}', //  {send}
                'buttons'=>[
                    'view'=>function ($url, $model) {
                        return Html::a( '<span class="glyphicon glyphicon-eye-open"></span>', $url,
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

/*
    Modal::begin([
        'header' => '<span></span>',
        'id' => 'maildata',
        'size' => Modal::SIZE_LARGE,
    ]);
    $this->renderAjax('send', ['model' => new SendmsgForm(), ]);
    Modal::end();
*/
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

EOT;
        $this->registerJs($sJs, View::POS_READY, 'showmodalmessage');
    ?>
    <div class="col-sm-12">
        <div class="form-group">
            <?= false ? ExportMenu::widget([
                'dataProvider' => $exportDataProvider,
                'filename' => 'user-messages',
                'exportConfig' => [
                    ExportMenu::FORMAT_HTML => false,
                    ExportMenu::FORMAT_CSV => false,
                    // ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_PDF => false,
                    /*[
                        'label' => Yii::t('kvexport', 'PDF'),
                        'icon' => $isFa ? 'file-pdf-o' : 'floppy-disk',
                        'iconOptions' => ['class' => 'text-danger'],
                        'linkOptions' => [],
                        'options' => ['title' => Yii::t('kvexport', 'Portable Document Format')],
                        'alertMsg' => Yii::t('kvexport', 'The PDF export file will be generated for download.'),
                        'mime' => 'application/pdf',
                        'extension' => 'pdf',
                        'writer' => 'PDF'
                    ],
*/
                ],
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
/*                    [
                        'class' => 'yii\grid\DataColumn',
                        'header' => 'Состояние',
                        'attribute' => 'flag.fl_sname',
                    ], */
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
                            return Html::decode($model->msg_pers_org);
                        },
                    ],
                    [
                        'attribute' => 'msg_pers_text',
                        'content' => function ($model, $key, $index, $column) {
                            /** @var Message $model */
                            return strip_tags(Html::decode($model->msg_pers_text));
                        },
                    ],
                    'msg_empl_command',
                    [
                        'attribute' => 'msg_answer',
                        'content' => function ($model, $key, $index, $column) {
                            /** @var Message $model */
                            return Html::decode(strip_tags($model->msg_answer));
                        },
                    ],
                    'msg_empl_remark',
                ]
            ]) : '' ?>
            <?php
                echo '<label class="control-label">Экспорт данных: &nbsp; </label>';

//                $sSearch = Urllocation::getSearchPart($searchModel);
//                if( $_SERVER['HTTP_HOST'] == 'host04.design' ) {
                    $aFormats = ['xls', 'xlsx', /*'pdf', 'html', 'docx'*/];
                    $param = [
                        'xls' => [
                            'icon' => 'fa-file-excel-o',
                            'text' => 'Excel',
                        ],
                        'xlsx' => [
                            'icon' => 'fa-file-excel-o',
                            'text' => 'Excel 2007',
                        ],
                        'pdf' => [
                            'icon' => 'fa-file-pdf-o',
                            'text' => 'Pdf',
                        ],
                        'html' => [
                            'icon' => 'fa-code',
                            'text' => 'Html',
                        ],
                        'docx' => [
                            'icon' => 'fa-file-word-o',
                            'text' => 'Word 2007',
                        ],
                    ];

                    foreach ($aFormats As $v) {
                        echo Html::a(
                            '<i class="fa '.$param[$v]['icon'].' fa-fw"></i> ' . $param[$v]['text'],
                            Url::to(array_merge(['export'], $searchModel->getSearchParams(), ['format' => $v])),
                            ['class' => 'btn btn-default', 'target' => '_blank']
                        );
                    }

                    $dStartPrevMonth = mktime(0, 0, 0, date('n')-1, 1, date('Y'));
                    $dStartCurMonth = mktime(0, 0, 0, date('n'), 1, date('Y'));
                    $dStartNextMonth = mktime(0, 0, 0, date('n')+1, 1, date('Y'));

                    $nFinQ = ceil(date('n') / 3) * 3;

                    $dStartPrevQuart = mktime(0, 0, 0, $nFinQ - 5, 1, date('Y'));
                    $dStartCurQuart = mktime(0, 0, 0, $nFinQ - 2, 1, date('Y'));
                    $dStartNextQuart = mktime(0, 0, 0, $nFinQ + 1, 1, date('Y'));

                    $aQuartDig = ['I', 'II', 'III', 'IV'];
                    $aMonth = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь', ];
                    $bWin = false;
/*
// на сервере не заработало
                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                        $sSet = setlocale(LC_ALL, 'russian');
                        $bWin = true;
                    } else {
//                        setlocale(LC_ALL, 'ru_RU');
                        $sSet = setlocale(LC_ALL, 'ru_RU.UTF-8');
                    }
*/
                    $sFormName = $searchModel->formName();
//                    Yii::info('CUR MONTH = ' . strftime('%B %Y', $dStartCurMonth) . ' ' . ($bWin ? 'WIN' : 'NOWIN'));

                    $aDropdata = [
                        [
//                            'title' => 'Текущий месяц (' . ($bWin ? iconv('CP1251', 'UTF-8', strftime('%B %Y', $dStartCurMonth)) : strftime('%B %Y', $dStartCurMonth)) . ')',
                            'title' => 'Текущий месяц (' . $aMonth[date('n', $dStartCurMonth) - 1] . ' ' . date('Y', $dStartCurMonth) . ')',
                            'url' => Url::to(
                                array_merge(
                                    ['export'],
                                    [$sFormName.'[msg_createtime]' => date('d.m.Y', $dStartCurMonth) . '-' . date('d.m.Y', $dStartNextMonth)],
                                    ['format' => 'xlsx']
                                )
                            ),
                            'icon' => 'fa-file-excel-o',
                        ],
                        [
//                            'title' => 'Предыдущий месяц (' . ($bWin ? iconv('CP1251', 'UTF-8', strftime('%B %Y', $dStartPrevMonth)) : strftime('%B %Y', $dStartPrevMonth)) . ')',
                            'title' => 'Предыдущий месяц (' . $aMonth[date('n', $dStartPrevMonth) - 1] . ' ' . date('Y', $dStartPrevMonth) . ')',
                            'url' => Url::to(
                                array_merge(
                                    ['export'],
                                    [$sFormName.'[msg_createtime]' => date('d.m.Y', $dStartPrevMonth) . '-' . date('d.m.Y', $dStartCurMonth)],
                                    ['format' => 'xlsx']
                                )
                            ),
                            'icon' => 'fa-file-excel-o',
                        ],
                        [
                            'title' => 'Текущий квартал (' . $aQuartDig[intval(date('n', $dStartCurQuart) / 3)] . ' ' . date('Y', $dStartCurQuart) . ')',
                            'url' => Url::to(
                                array_merge(
                                    ['export'],
                                    [$sFormName.'[msg_createtime]' => date('d.m.Y', $dStartCurQuart) . '-' . date('d.m.Y', $dStartNextQuart)],
                                    ['format' => 'xlsx']
                                )
                            ),
                            'icon' => 'fa-file-excel-o',
                        ],
                        [
                            'title' => 'Предыдущий квартал (' . $aQuartDig[intval(date('n', $dStartPrevQuart) / 3)] . ' ' . date('Y', $dStartPrevQuart) . ')',
                            'url' => Url::to(
                                array_merge(
                                    ['export'],
                                    [$sFormName.'[msg_createtime]' => date('d.m.Y', $dStartPrevQuart) . '-' . date('d.m.Y', $dStartCurQuart)],
                                    ['format' => 'xlsx']
                                )
                            ),
                            'icon' => 'fa-file-excel-o',
                        ],
                    ];

                    $aIem = [];
                    foreach($aDropdata As $v) {
                        $aIem[] = Html::a(
                            $v['title'], // '<i class="fa '.$v['icon'].' fa-fw"></i> ' .
                            $v['url'],
                            ['target' => '_blank'] // 'class' => 'btn btn-default',
                        );
                    }
                    echo "\n\n" . Html::tag(
                        'div',
                        Html::button(
                            'Все сообщения за ' . Html::tag('span', '', ['class' => 'caret',]),
                            [
                                'type' => 'button',
                                'class' => 'btn btn-default dropdown-toggle',
                                'data-toggle' => 'dropdown',
                                'aria-expanded' => 'false',
                            ]
                        ) . Html::ul($aIem, ['encode' => false, 'class'=>"dropdown-menu", 'role'=>"menu", ]), // itemOptions
                        ['class' => 'btn-group']
                    ) . "\n\n";
//                }
            // http://host04.design/message/export?msg_id=&msg_createtime=&msg_pers_lastname=%D0%BB%D0%B0&msg_pers_email=&msg_pers_org=14&msg_empl_id=&msg_flag%5B0%5D=2&msg_flag%5B1%5D=3&msg_flag%5B2%5D=4&msg_flag%5B3%5D=5&msg_flag%5B4%5D=6&msg_flag%5B5%5D=7&msg_flag%5B6%5D=8&msg_flag%5B7%5D=9&msg_flag%5B8%5D=10&msg_flag%5B9%5D=12&msg_subject=
            ?>
        </div>
    </div>

</div>
