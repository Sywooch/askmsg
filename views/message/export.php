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

$styleTitle = array(
    'font' => array(
        'bold' => true,
        'size' => 18,
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    ),
);

$styleColTitle = array(
    'font' => array(
        'bold' => true,
        'size' => 14,
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'wrap' => true
    ),
);

$styleSell = array(
    'font' => array(
        'bold' => false,
        'size' => 10,
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
        'wrap' => true
    ),
);


$oSheet->getColumnDimension('A')->setWidth(14);
$oSheet->getColumnDimension('B')->setWidth(20);
$oSheet->getColumnDimension('C')->setWidth(30);
$oSheet->getColumnDimension('D')->setWidth(40);
$oSheet->getColumnDimension('E')->setWidth(80);
$oSheet->setCellValue('A1', Yii::$app->name)
    ->setCellValue('A2', 'Выгрузка от ' . date('d.m.Y H:i'));
$objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
$objPHPExcel->getActiveSheet()->mergeCells('A2:E2');
$oSheet->getStyle('A1')->applyFromArray($styleTitle);
$oSheet->getStyle('A2')->applyFromArray($styleTitle);

$oSheet->fromArray(
    [
        '№',
        'Обращение' . "\r\n" . 'Дата',
        'Фамилия Имя Отчество',
        'Тема' . "\r\n" . 'Учреждение',
        'Обращение'
    ],
    null,
    'A4'
);
$oSheet->getStyle('A4:E4')->applyFromArray($styleColTitle);

$cou = 1;
$nRow = 5;
$oSheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, $nRow-1);

for($page = 0; $page < $nPageCount; $page++) {
    $dataProvider->pagination->setPage($page);
    $dataProvider->refresh();
    foreach($dataProvider->getModels() As $model) {
        $oSheet->fromArray(
            [
                $cou,
                $model->msg_id . "\r\n" . date("d.m.Y", strtotime($model->msg_createtime)) . "\r\n" . $model->flag->fl_sname,
                $model->getFullName() . "\r\n" . $model->msg_pers_email . "\r\n" . $model->msg_pers_phone . "\r\n\r\n" . (($model->msg_empl_id !== null) ? $model->employee->getFullName() : ''),
                ($model->subject ? ($model->subject->tag_title . "\r\n") : '') . $model->msg_pers_org,
                $model->msg_pers_text
            ],
            null,
            'A' . $nRow
        );
        $oSheet->getStyle('A'.$nRow.':E' . ($nRow))->applyFromArray($styleSell);
        $cou++;
//            ->setCellValue('A' . $nRow, $model->msg_id . "\n" . date("d.m.Y", strtotime($model->msg_createtime)))
//            ->setCellValue('B' . $nRow, $model->getFullName() . "\n" . $model->msg_pers_email . " " . $model->msg_pers_phone)
//            ->setCellValue('C' . $nRow, ($model->subject ? ($model->subject->tag_title . "\n") : '') . $model->msg_pers_org)
//            ->setCellValue('D' . $nRow, $model->msg_pers_text);
        $nRow++;
    }
}

$oSheet->getPageSetup()->setPrintArea('A1:E' . ($nRow - 1));

$styleBorders = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);
$oSheet->getStyle('A4:E' . ($nRow - 1))->applyFromArray($styleBorders);

$sFilename = $_SERVER['HTTP_HOST'].'-export-'.date('YmdHis').'.'.$format;
$sf = Yii::getAlias('@webroot/upload/export');

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
    $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
}
else if( $format == 'xlsx' ) {
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
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

