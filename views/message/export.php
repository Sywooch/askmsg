<?php
/**
 * User: KozminVA
 * Date: 07.04.2015
 * Time: 15:23
 *
 *
 * @var ActiveDataProvider $dataProvider
 * @var Message $model
 */

use yii\data\ActiveDataProvider;
use app\models\Message;


$dataProvider->prepare();
$nMaxCount = 1000;

echo $format;
echo ' ' . $dataProvider->pagination->pageCount;
echo ' ' . $dataProvider->pagination->totalCount;

$objPHPExcel = new PHPExcel();
$oSheet = $objPHPExcel->getSheet(0);

$objPHPExcel->getProperties()
    ->setCreator(Yii::$app->name)
    ->setLastModifiedBy(Yii::$app->name)
    ->setTitle(Yii::$app->name)
    ->setSubject("Export " . date('d.m.Y H:i:s'))
    ->setDescription("Export " . date('d.m.Y H:i:s'))
    ->setKeywords(Yii::$app->name)
    ->setCategory(Yii::$app->name);

$oSheet->getPageSetup()
    ->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)
    ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

$oSheet->getPageMargins()
    ->setTop(1)
    ->setRight(0.75)
    ->setLeft(0.75)
    ->setBottom(1);

$nPageCount = $dataProvider->pagination->pageCount;

if( $dataProvider->pagination->totalCount > $nMaxCount ) {
    $nPageCount = floor($nMaxCount / $dataProvider->pagination->pageSize);
}

$cou = 0;
$nRow = 4;
$oSheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, $nRow-1);
$oSheet->getColumnDimension('A')->setWidth(20);
$oSheet->getColumnDimension('B')->setWidth(30);
$oSheet->getColumnDimension('C')->setWidth(40);
$oSheet->getColumnDimension('D')->setWidth(80);

for($page = 0; $page < $nPageCount; $page++) {
    $dataProvider->pagination->setPage($page);
    $dataProvider->refresh();
    foreach($dataProvider->getModels() As $model) {
        $oSheet->fromArray(
            [
                $model->msg_id . "\n" . date("d.m.Y", strtotime($model->msg_createtime)) . "\n" . $model->flag->fl_sname,
                $model->getFullName() . "\n" . $model->msg_pers_email . " " . $model->msg_pers_phone . "\n" . (($model->msg_empl_id !== null) ? $model->employee->getFullName() : ''),
                ($model->subject ? ($model->subject->tag_title . "\n") : '') . $model->msg_pers_org,
                $model->msg_pers_text
            ],
            null,
            'A' . $nRow
        );
//            ->setCellValue('A' . $nRow, $model->msg_id . "\n" . date("d.m.Y", strtotime($model->msg_createtime)))
//            ->setCellValue('B' . $nRow, $model->getFullName() . "\n" . $model->msg_pers_email . " " . $model->msg_pers_phone)
//            ->setCellValue('C' . $nRow, ($model->subject ? ($model->subject->tag_title . "\n") : '') . $model->msg_pers_org)
//            ->setCellValue('D' . $nRow, $model->msg_pers_text);
        $nRow++;
    }
}

$oSheet->getPageSetup()->setPrintArea('A1:D' . ($nRow - 1));

$sFilename = $_SERVER['HTTP_HOST'].'-export-'.date('YmdHis').'.'.$format;
$sf = Yii::getAlias('@web/upload/export');
if( !is_dir($sf) ) {
    mkdir($sf);
}
$sf .= DIRECTORY_SEPARATOR . $sFilename;

//$headers = Yii::$app->response->headers;
//$headers->set('Content-Type', 'application/' . $format);
//$headers->set('Content-Disposition', 'attachment;filename="'.$sFilename.'"');
//$headers->set('Cache-Control', 'max-age=0');
//Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;

if( $format == 'xls' ) {
    $objWriter = PHPExcel_Writer_Excel5($objPHPExcel);
}
else if( $format == 'xlsx' ) {
    $objWriter = PHPExcel_Writer_Excel2007($objPHPExcel);
}
else if( $format == 'pdf' ) {
    $rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
    $rendererLibraryPath = Yii::getAlias('@vendor/kartik-v/mpdf');
    if (!PHPExcel_Settings::setPdfRenderer(
        $rendererName,
        $rendererLibraryPath
    )) {
        die(
            'Please set the $rendererName and $rendererLibraryPath values' .
            PHP_EOL .
            ' as appropriate for your directory structure'
        );
    }
    $objWriter = new PHPExcel_Writer_PDF($objPHPExcel);
}


$objWriter->save($sf);
Yii::$app->response->sendFile($sf);

