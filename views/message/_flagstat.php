<?php

use yii\helpers\Html;
use yii\db\Query;
use yii\helpers\ArrayHelper;

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
        ->leftJoin(Message::tableName() . ' m', 'f.fl_id = m.msg_flag And m.msg_empl_id = ' . Yii::$app->user->identity->getId())
//        ->where('f.fl_id In (' . implode(',', $aStatFlags) . ')')
        ->groupBy(['f.fl_name', 'f.fl_id', 'f.fl_sname']);

    if( $bOnlyLoggedUser ) {
        $query->andWhere([]);
    }

    Yii::info('SQL FILTER = ' . print_r($query->createCommand()->getRawSql(), true));

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
    foreach($aStatFlags As $v) {
        if( !isset($aData[$v]) ) {
            continue;
        }
//        $s = trim(preg_replace('|^\\[[^\\]]+\\]|', '', $ad['fl_name']));
        $s = trim($aData[$v]['fl_sname']);
        echo Html::a($s . ' ' . $aData[$v]['cou'], '?' . Html::getInputName(new MessageSearch(), 'msg_flag') . '=' . $v, ['class'=>'btn btn-success', 'role'=>"button"]);
    }
    ?>
    </div>
    <?php
}

?>

