<?php

use yii\helpers\Html;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\View;

use app\models\Msgflags;
use app\models\Message;
use app\models\MessageSearch;

/* @var $this yii\web\View */
$aStatFlags = [
    Msgflags::MFLG_SHOW_INSTR,
    Msgflags::MFLG_INT_INSTR,
    Msgflags::MFLG_SHOW_REVIS,
    Msgflags::MFLG_INT_REVIS_INSTR,
    Msgflags::MFLG_SHOW_ANSWER,
    Msgflags::MFLG_INT_FIN_INSTR,
];

if( !isset($bOnlyLoggedUser) ) {
    $bOnlyLoggedUser = false;
}


if( !$bOnlyLoggedUser ) {
    $aStatFlags = array_merge(
        $aStatFlags,
        [
            Msgflags::MFLG_INT_NEWANSWER,
            Msgflags::MFLG_SHOW_NEWANSWER,
            Msgflags::MFLG_SHOW_NOSOGL,
            Msgflags::MFLG_INT_NOSOGL,
        ]);

    $aMainStat = [
        Msgflags::MFLG_SHOW_INSTR,
        Msgflags::MFLG_INT_INSTR,
        Msgflags::MFLG_SHOW_REVIS,
        Msgflags::MFLG_INT_REVIS_INSTR,
        Msgflags::MFLG_INT_NEWANSWER,
        Msgflags::MFLG_SHOW_NEWANSWER,
        Msgflags::MFLG_SHOW_NOSOGL,
        Msgflags::MFLG_INT_NOSOGL,
    ];
}
// Статистику получаем из кеша или вычисляем и кладем в кеш

$statKey = Message::KEY_STATMSG_DATA;

if( $bOnlyLoggedUser ) {
    $statKey .= '_' . Yii::$app->user->identity->getId();
}
$aStat = Yii::$app->cache->get($statKey);
if( $aStat === false ) {
    // Left Join variant
    $query = (new Query())
        ->select(['COUNT(m.msg_id) As cou', 'f.fl_name', 'f.fl_id', 'f.fl_sname'])
        ->from([Msgflags::tableName() . ' f'])
        ->leftJoin(Message::tableName() . ' m', 'f.fl_id = m.msg_flag' . ($bOnlyLoggedUser ? (' And m.msg_empl_id = ' . Yii::$app->user->identity->getId()) : '') )
//        ->where('f.fl_id In (' . implode(',', $aStatFlags) . ')')
        ->groupBy(['f.fl_name', 'f.fl_id', 'f.fl_sname']);

    $aStat = $query->createCommand()->queryAll();
    if( !$bOnlyLoggedUser ) {
        Yii::$app->cache->set(Message::KEY_STATMSG_DATA, $aStat, 3600);
    }
}

$aData = ArrayHelper::map(
    $aStat,
    'fl_id',
    function($item) { return $item; }
);

if( count($aData) > 0 ) {
    ?>
    <div>
    <?php
    $aDop = [];
    foreach($aStatFlags As $v) {
        if( !isset($aData[$v]) ) {
            continue;
        }
//        $s = trim(preg_replace('|^\\[[^\\]]+\\]|', '', $ad['fl_name']));
        $s = trim($aData[$v]['fl_sname']);
        $sLink = Html::a(
            $s . ' ' . $aData[$v]['cou'],
            '?' . Html::getInputName(new MessageSearch(), 'msg_flag') . '=' . $v,
            [
                'class'=>'btn btn-success faststatlink',
                'role'=>"button",
            ]
        );
        if( !isset($aMainStat) || in_array($v, $aMainStat)) {
            echo $sLink;
        }
        else {
            $aDop[] = Html::a(
                $s . ' ' . $aData[$v]['cou'],
                '?' . Html::getInputName(new MessageSearch(), 'msg_flag') . '=' . $v,
                ['style' => 'margin: 3px 0;']
            );
        }
    }
    if( count($aDop) > 0 ) {
        echo Html::tag(
            'div',
            Html::a(
                '<span class="caret"></span>',
                '#',
                [
                    'data-target' => '#',
                    'data-toggle' => 'dropdown',
                    'role' => 'button',
                    'aria-haspopup' => 'true',
                    'aria-expanded' => 'false'
                ]
            )
            . Html::tag(
                'ul',
                '<li>' . implode('</li><li>', $aDop) . '</li>',
                [
                    'class' => 'dropdown-menu nohoverlink',
                    'aria-labelledby' => 'dLabel'
                ]
            ),
            [
                'class' => 'dropdown',
                'style' => 'display: inline-block;',
            ]
//            <div class="dropdown">
//
//  <ul class="dropdown-menu" aria-labelledby="dLabel">
//  </ul>
//</div>

        );
    }
    /*
<div class="dropdown">
    <a data-target="#" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></a>
  <ul class="dropdown-menu" aria-labelledby="dLabel">
  </ul>
</div>
    */
    ?>
    </div>
    <?php

$sJs = <<<EOT
if( window.location.search.indexOf("&") == -1 ) {
    jQuery(".faststatlink[href='"+window.location.search+"']")
        .removeClass("btn-success")
        .addClass("btn-primary");
}

EOT;

    $this->registerJs($sJs, View::POS_READY, 'statlinktest');
}


?>

