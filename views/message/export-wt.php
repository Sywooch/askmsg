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
use yii\web\NotFoundHttpException;
use app\models\Message;
use app\components\Exportutil;

$mime = [
    'xls' => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
];
$dataProvider->prepare();
$nMaxCount = 2500;

echo $format;
echo ' ' . $dataProvider->pagination->pageCount;
echo ' ' . $dataProvider->pagination->totalCount;

$objPHPExcel = new PHPExcel();
$oSheet = $objPHPExcel->getSheet(0);

$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
// $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_discISAM;
// $cacheSettings = array( 'dir'  => '/usr/local/tmp');

// PHPExcel_Settings::setCacheStorageMethod($cacheMethod);

$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
$cacheSettings = ['memoryCacheSize'  => '8MB'];
// PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_sqlite3;
$bCache = PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
// Yii::info('cache to sqlite3: ' . print_r($bCache, true));

$oDefaultStyle = $objPHPExcel->getDefaultStyle();
$oDefaultStyle->getFont()->setName('Arial');
$oDefaultStyle->getFont()->setSize(8);
/*
$objPHPExcel->getProperties()
    ->setCreator(Yii::$app->name)
    ->setLastModifiedBy(Yii::$app->name)
    ->setTitle(Yii::$app->name)
    ->setSubject("Export " . date('d.m.Y H:i:s'))
    ->setDescription("Export " . date('d.m.Y H:i:s'))
    ->setKeywords(Yii::$app->name)
    ->setCategory(Yii::$app->name);
*/

$oSheet->getPageSetup()
    ->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT) // ORIENTATION_LANDSCAPE
    ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
    ->setFitToPage(true)
    ->setFitToWidth(1)
    ->setFitToHeight(0);

$oSheet->getPageMargins()
    ->setTop(0.5)
    ->setRight(0.35)
    ->setLeft(0.35)
    ->setBottom(1);
$oSheet->getHeaderFooter()
    ->setEvenFooter('&CСтраница &P [&N]')
    ->setOddFooter('&CСтраница &P [&N]');

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
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
    ),
);

$styleColTitle = array(
    'font' => array(
        'bold' => true,
        'size' => 14,
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
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


$oSheet->getColumnDimension('A')->setWidth(16);
$oSheet->getColumnDimension('B')->setWidth(50);
$oSheet->getColumnDimension('C')->setWidth(50);
$oSheet->getColumnDimension('D')->setWidth(40);
//$oSheet->getColumnDimension('D')->setAutoSize(true);
//$oSheet->getColumnDimension('E')->setWidth(80);
$oSheet->setCellValue('A1', Yii::$app->name)
    ->setCellValue('A2', 'Выгрузка от ' . date('d.m.Y H:i'));
$objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
$objPHPExcel->getActiveSheet()->mergeCells('A2:D2');
$oSheet->getStyle('A1')->applyFromArray($styleTitle);
$oSheet->getStyle('A2')->applyFromArray($styleTitle);

$oSheet->fromArray(
    [
//        '№',
        '№' . "\r\n" . 'Дата',
        'Состояние',
        'Проситель' . "\r\n" . 'Исполнитель',
        'Контакты',
    ],
    null,
    'A4'
);
$oSheet->getStyle('A4:D4')->applyFromArray($styleColTitle);

$cou = 1;
$nStartRow = 5;
$nRow = $nStartRow;
$oSheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, $nRow-1);

for($page = 0; $page < $nPageCount; $page++) {
    $dataProvider->pagination->setPage($page);
    $dataProvider->refresh();

    foreach($dataProvider->getModels() As $model) {
        $oSheet->fromArray(
            [
                $model->msg_id . "\r\n" . date("d.m.Y", strtotime($model->msg_createtime)),
                preg_replace('|^\\[[^\\]]+\\]\\s+|', '', $model->flag->fl_name),
                $model->getFullName() . (($model->msg_empl_id !== null) ?  ("\r\n" . $model->employee->getFullName()) : ''),
                $model->msg_pers_email . "\r\n" . $model->msg_pers_phone,
            ],
            null,
            'A' . $nRow
        );
        $cou++;
        $nRow++;
    }
}

$oStyle = $oSheet->getStyle('A'.$nStartRow.':D' . ($nRow-1));
$oStyle->applyFromArray($styleSell);
$oStyle->getAlignment()->setWrapText(true);
$oStyle->getAlignment()->setIndent(1);

$styleBorders = [
    'borders' => [
        'allborders' => [
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ],
        'outline' => [
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ],
    ],
];

$oSheet->getStyle('A4:D' . ($nRow - 1))->applyFromArray($styleBorders);

$oSheet->getPageSetup()->setPrintArea('A1:D' . ($nRow - 1));


$oUtil = new Exportutil();
$sFilename = $_SERVER['HTTP_HOST'].'-export-'.date('YmdHis').'.'.$format;
$sf = $oUtil->getFilePath($sFilename);

//$headers = Yii::$app->response->headers;
//$headers->set('Content-Type', 'application/' . $format);
//$headers->set('Content-Disposition', 'attachment;filename="'.$sFilename.'"');
//$headers->set('Cache-Control', 'max-age=0');
//Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;

$objWriter = null;

if( $format == 'xls' ) {
    $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
}
else if( $format == 'xlsx' ) {
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
}
else if( $format == 'pdf' ) {
//    $rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
//    $rendererLibraryPath = Yii::getAlias('@vendor/kartik-v/mpdf');

    $rendererName = PHPExcel_Settings::PDF_RENDERER_TCPDF;
    $rendererLibraryPath = Yii::getAlias('@vendor/tecnick.com/tcpdf');

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
    $objPHPExcel->setActiveSheetIndex(0);
    $objWriter = new PHPExcel_Writer_PDF($objPHPExcel);
    $objWriter->writeAllSheets();
//    $objWriter->SetFont('times', '', 10);
//    $objWriter->setSheetIndex(0);
}
else if( $format == 'html' ) {
    $objWriter = new PHPExcel_Writer_HTML($objPHPExcel);
}

if( !$objWriter ) {
    throw new NotFoundHttpException('The requested page does not exist.');
}

$objWriter->save($sf);
Yii::$app->response->sendFile($sf);

