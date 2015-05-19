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
use yii\helpers\Html;

use app\models\Message;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use app\components\Exportutil;

/**
 * конвертируем текст для word
 * @param $element
 * @param $text
 * @param null $style
 */
function insertText($element, $text, $style = null) {
    $a = explode("\n", str_replace("\r", "\n", $text));
    foreach($a As $k=>$v) {
        $v = trim($v);
        if( $v == '' ) {
            continue;
        }
//        if( $k > 0 ) {
//            $element->addTextBreak();
//        }
        if( $style !== null ) {
            $element->addText(htmlspecialchars($v), $style);
        }
        else {
            $element->addText(htmlspecialchars($v));
        }
    }
}
$dataProvider->prepare();
$nMaxCount = 500;

$nPageCount = $dataProvider->pagination->pageCount;
$cou = 1;
$nRow = 1;

if( $dataProvider->pagination->totalCount > $nMaxCount ) {
    $nPageCount = floor($nMaxCount / $dataProvider->pagination->pageSize);
}


// echo $format;
// echo ' ' . $dataProvider->pagination->pageCount;
// echo ' ' . $dataProvider->pagination->totalCount;

$phpWord = new PhpWord();
/*
$properties = $phpWord->getDocInfo();
$properties->setCreator('My name');
$properties->setCompany('My factory');
$properties->setTitle('My title');
$properties->setDescription('My description');
$properties->setCategory('My category');
$properties->setLastModifiedBy('My name');
$properties->setCreated(mktime(0, 0, 0, 3, 12, 2014));
$properties->setModified(mktime(0, 0, 0, 3, 14, 2014));
$properties->setSubject('My subject');
$properties->setKeywords('my, key, word');
*/

$n1sm = \PhpOffice\PhpWord\Shared\Converter::inchToTwip(1/2.54);
$n1_5sm = \PhpOffice\PhpWord\Shared\Converter::inchToTwip(1.5/2.54);
$n0_4sm = \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.4/2.54);
$n2sm = \PhpOffice\PhpWord\Shared\Converter::inchToTwip(2/2.54);
$n5_2sm = \PhpOffice\PhpWord\Shared\Converter::inchToTwip(5.2/2.54);
$n15sm = \PhpOffice\PhpWord\Shared\Converter::inchToTwip(15/2.54);

$sectionStyle = array(
    'orientation' => 'landscape',
    'marginTop' => $n1sm,
    'marginBottom' => $n1_5sm,
    'marginLeft' => $n1sm,
    'marginRight' => $n1sm,
);

$section = $phpWord->addSection($sectionStyle);

$footer = $section->addFooter();
$footer->addPreserveText(
    htmlspecialchars('Страница {PAGE} [{NUMPAGES}]'),
    array('align' => 'center')
);

$styleTable = array('borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => $n0_4sm);
$styleFirstRow = array('borderBottomSize' => 18, 'borderBottomColor' => '000000', 'bgColor' => 'eeeeee');
$styleHeaderCell = array('valign' => 'center');
$styleCell = array('valign' => 'top');
$fontHeaderStyle = array('bold' => true, 'align' => 'center', 'alignment' => 'center');
$fontStyle = array('bold' => false, 'align' => 'center', 'alignment' => 'center');

$phpWord->addTableStyle('Msg Table', $styleTable, $styleFirstRow);
$table = $section->addTable('Msg Table');

$table->addRow($n2sm);
$bLocal = ($_SERVER['HTTP_HOST'] == 'host04.design');
$aWidth = [
    1150,
    3700,
    3700,
    7300
];
/*
$aWidth1 = [
    $aWidth[0],
    $bLocal ? $aWidth[1] : array_reduce(array_slice($aWidth, 0, 2), function($carry, $item) { return $carry + $item;}, 0),
    $bLocal ? $aWidth[2] : array_reduce(array_slice($aWidth, 0, 3), function($carry, $item) { return $carry + $item;}, 0),
    $bLocal ? $aWidth[3] : array_reduce(array_slice($aWidth, 0, 4), function($carry, $item) { return $carry + $item;}, 0),
];
*/

insertText($table->addCell($aWidth[0], $styleHeaderCell), '№' . "\r\n" . 'Дата', $fontHeaderStyle);
insertText($table->addCell($aWidth[1], $styleHeaderCell), 'Фамилия Имя Отчество' . "\r\n" . 'Контакты', $fontHeaderStyle);
insertText($table->addCell($aWidth[2], $styleHeaderCell), 'Тема' . "\r\n" . 'Учреждение', $fontHeaderStyle);
insertText($table->addCell($bLocal ? $aWidth[3] : $aWidth[3] / 3, $styleHeaderCell), 'Обращение', $fontHeaderStyle);
// $table->addCell($n2sm, $styleCell)->addText(prepare('№' . "\r\n" . 'Дата'), $fontStyle);
//$table->addCell($n5_2sm, $styleCell)->addText(prepare('Фамилия Имя Отчество' . "\r\n" . 'Контакты'), $fontStyle);
//$table->addCell($n5_2sm, $styleCell)->addText(prepare('Тема' . "\r\n" . 'Учреждение'), $fontStyle);
//$table->addCell($n15sm, $styleCell)->addText(prepare('Обращение'), $fontStyle);

for($page = 0; $page < $nPageCount; $page++) {
//    Yii::info(str_repeat('-', 30) . ' Page: ' . $page . ' ['.$cou.']');
    $dataProvider->pagination->setPage($page);
    $dataProvider->refresh();
    foreach($dataProvider->getModels() As $model) {
        $table->addRow();
        insertText($table->addCell($aWidth[0], $styleCell), $model->msg_id . "\r\n" . date("d.m.Y", strtotime($model->msg_createtime)) . "\r\n" . $model->flag->fl_sname, $fontStyle);
        insertText($table->addCell($aWidth[1], $styleCell), $model->getFullName() . "\r\n" . $model->msg_pers_email . "\r\n" . $model->msg_pers_phone . "\r\n\r\n" . (($model->msg_empl_id !== null) ? $model->employee->getFullName() : ''), $fontStyle);
        insertText($table->addCell($aWidth[2], $styleCell), ($model->subject ? ($model->subject->tag_title . "\r\n") : '') . $model->msg_pers_org, $fontStyle);
        insertText($table->addCell($bLocal ? $aWidth[3] : $aWidth[3] / 3, $styleCell), Html::decode($model->msg_pers_text), $fontStyle);
/*
        $table->addCell($n2sm)->addText(prepare($model->msg_id . "\r\n" . date("d.m.Y", strtotime($model->msg_createtime)) . "\r\n" . $model->flag->fl_sname));
        $table->addCell($n5_2sm)->addText(prepare($model->getFullName() . "\r\n" . $model->msg_pers_email . "\r\n" . $model->msg_pers_phone . "\r\n\r\n" . (($model->msg_empl_id !== null) ? $model->employee->getFullName() : '')));
        $table->addCell($n5_2sm)->addText(prepare(($model->subject ? ($model->subject->tag_title . "\r\n") : '') . $model->msg_pers_org));
        $table->addCell($n15sm)->addText(prepare($model->msg_pers_text));
*/
        $cou++;
        $nRow++;
    }
}

$oUtil = new Exportutil();
$sFilename = $_SERVER['HTTP_HOST'].'-export-'.date('YmdHis').'.'.$format;
$sf = $oUtil->getFilePath($sFilename);

$objWriter = null;

if( $format == 'docx' ) {
    $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
}
else if( $format == 'rtf' ) {
    $objWriter = IOFactory::createWriter($phpWord, 'RTF');
}

if( !$objWriter ) {
    throw new NotFoundHttpException('The requested page does not exist.');
}

$objWriter->save($sf);
Yii::$app->response->sendFile($sf);

