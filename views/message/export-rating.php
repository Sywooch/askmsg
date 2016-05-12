<?php

set_time_limit(0);

/* @var $this yii\web\View */
/* @var $model app\models\ExportdataForm */

use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

use app\models\Message;
use app\models\ExportdataForm;
use app\models\Msgflags;
use app\components\Exportutil;

$mime = [
    'csv' => 'application/vnd.ms-excel',
    'xls' => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
];

$format = 'csv';

$dataProvider = $model->prepareDataForSearch();
$dataProvider->prepare();
// $nMaxCount = 2500;

// echo $format;
// echo ' ' . $dataProvider->pagination->pageCount;
// echo ' ' . $dataProvider->pagination->totalCount;
Yii::info('pageCount = ' . $dataProvider->pagination->pageCount . ' totalCount = ' . $dataProvider->pagination->totalCount);

$nMaxCount = $dataProvider->pagination->totalCount;
$nPageCount = $dataProvider->pagination->pageCount;

Yii::info(basename(__FILE__) . ': ' . $format. ' ' . $dataProvider->pagination->pageCount . ' ' . $dataProvider->pagination->totalCount);

$sSeparator = ';';
$sLineEnd = "\n";

$oUtil = new Exportutil();
$sFilename = $_SERVER['HTTP_HOST'].'-export-'.date('YmdHis').'.'.$format;
$sf = $oUtil->getFilePath($sFilename);

$fp = fopen($sf, 'w');

$aFldNames = $model->prepareFieldNames();
$a = [];
foreach($model->fieldslist As $attrName) {
    $a[] = $model->prepareCsvValue($aFldNames[$attrName]);
}
fwrite($fp, implode($sSeparator, $a) . $sLineEnd);
$cou = 0;

for($page = 0; $page < $nPageCount; $page++) {
    $dataProvider->pagination->setPage($page);
    $dataProvider->refresh();

    foreach($dataProvider->getModels() As $ob) {
        Yii::info(print_r($ob->attributes, true));
        $a = [];
        foreach($model->fieldslist As $attrName) {
            $sVal = $model->prepareCsvValue($model->getFieldValue($ob, $attrName));
            if( $attrName == 'msg_flag' ) {
                $sVal = $ob->msg_flag;
            }
            $sAttr = isset($ob->attributes[$attrName]) ? $ob->$attrName : 'noattr';
            Yii::info($attrName . ' = ' . (is_array($sAttr) ? 'array' : $sAttr) . ' -> ' . $sVal);
            $a[] = $sVal;
        }
        fwrite($fp, implode($sSeparator, $a) . $sLineEnd);
        $cou++;
        if( in_array($ob->msg_flag, [Msgflags::MFLG_INT_FIN_INSTR, Msgflags::MFLG_SHOW_ANSWER]) ) {
            $a[1] = ($ob->msg_answertime === null) ? $ob->msg_createtime : $ob->msg_answertime;
            fwrite($fp, implode($sSeparator, $a) . $sLineEnd);
            $cou++;
        }
    }
}

fclose($fp);
Yii::$app->response->sendFile($sf);
