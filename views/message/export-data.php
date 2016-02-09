<?php

set_time_limit(0);

/* @var $this yii\web\View */
/* @var $model app\models\ExportdataForm */

use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

// use app\models\Message;
use app\models\ExportdataForm;
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
        $a = [];
        foreach($model->fieldslist As $attrName) {
            $a[] = $model->prepareCsvValue($model->getFieldValue($ob, $attrName));
        }
        fwrite($fp, implode($sSeparator, $a) . $sLineEnd);
        $cou++;
    }
}

fclose($fp);
Yii::$app->response->sendFile($sf);
