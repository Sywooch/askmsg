<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 30.12.2015
 * Time: 11:56
 */

namespace app\components;

use yii;
use Closure;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\base\InvalidValueException;
use yii\data\ActiveDataProvider;

use PHPExcel;
use PHPExcel_CachedObjectStorageFactory;
use PHPExcel_Settings;
use PHPExcel_Writer_Excel5;
use PHPExcel_Writer_Excel2007;

class ExcelexportBehavior extends Behavior {
    /**
     * @var string
     */
    public $dataTitle = null;

    /**
     * @var integer
     */
    public $nStartRow = 1;

    /**
     * @var array column titles
     */
    public $columnTitles = [];

    /**
     * @var array column width
     */
    public $columnWidth = [];

    /**
     * @var array column values
     */
    public $columnValues = [];

    /**
     *
     */
    public function init() {
        parent::init();
        if( !is_array($this->columnValues) || (count($this->columnValues) == 0) ) {
            throw new \InvalidArgumentException('Нужно задать значения для вывода');
        }
    }

    /**
     * Вывод в файл
     * @param ActiveDataProvider $dataProvider
     * @param string $sFileName
     * @return boolean
     */
    public function exportToFile($dataProvider, $sFileName) {
        $dataProvider->prepare();
        $nMaxCount = $dataProvider->pagination->totalCount;


        $objPHPExcel = new PHPExcel();
        $oSheet = $objPHPExcel->getSheet(0);

        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_sqlite3;
        $bCache = PHPExcel_Settings::setCacheStorageMethod($cacheMethod);

        $oDefaultStyle = $objPHPExcel->getDefaultStyle();
        $oDefaultStyle->getFont()->setName('Arial');
        $oDefaultStyle->getFont()->setSize(8);

//        $oSheet->getPageSetup()
//            ->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT) // ORIENTATION_LANDSCAPE
//            ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
//            ->setFitToPage(true)
//            ->setFitToWidth(1)
//            ->setFitToHeight(0);
//
//        $oSheet->getPageMargins()
//            ->setTop(0.5)
//            ->setRight(0.35)
//            ->setLeft(0.35)
//            ->setBottom(1);
//        $oSheet->getHeaderFooter()
//            ->setEvenFooter('&CСтраница &P [&N]')
//            ->setOddFooter('&CСтраница &P [&N]');

        $nPageCount = $dataProvider->pagination->pageCount;

        if( $dataProvider->pagination->totalCount > $nMaxCount ) {
            $nPageCount = floor($nMaxCount / $dataProvider->pagination->pageSize);
        }

        $cou = 0;
        $nRow = $this->nStartRow;

        foreach($this->columnWidth as $k=>$v) {
            if( $v !== null ) {
                $oSheet->getColumnDimension($this->colIndexToName($k+1))->setWidth($v);
            }
        }

        $sLastCol = $this->colIndexToName(
            max(
                count($this->columnWidth),
                count($this->columnTitles),
                count($this->columnValues)
            )
        );

        $n = $nRow;
        $sTit = 'A'.$nRow++;
        $oSheet
            ->setCellValue($sTit, 'Выгрузка от ' . date('d.m.Y H:i'));
        $objPHPExcel->getActiveSheet()->mergeCells($sTit . ':' . $sLastCol . $n);

        $sdataTitle = ($this->dataTitle === null ) ? Yii::$app->name : $this->dataTitle;
        if( $sdataTitle != '' ) {
            $n = $nRow;
            $sTit = 'A'.$nRow++;
            $oSheet
                ->setCellValue($sTit, $sdataTitle);
            $objPHPExcel->getActiveSheet()->mergeCells($sTit . ':' . $sLastCol . $n);
        }

        if( count($this->columnTitles) > 0 ) {
            $nRow++;
            $oSheet->fromArray(
                $this->columnTitles,
                null,
                'A' . $nRow++
            );
        }

        for($page = 0; $page < $nPageCount; $page++) {
            $dataProvider->pagination->setPage($page);
            $dataProvider->refresh();

            foreach ($dataProvider->getModels() As $model) {
                $aData = [];
                foreach($this->columnValues As $v) {
                    $aData[] = ($v instanceof Closure) ? call_user_func($v, $model, $cou) : $model->$v;
                }

                $oSheet->fromArray(
                    $aData,
                    null,
                    'A' . $nRow
                );

                $cou++;
                $nRow++;
            }
        }

        $oSheet->getPageSetup()->setPrintArea('A'.$this->nStartRow.':' . $sLastCol . ($nRow - 1));

        $format = $this->getFileExt($sFileName, 'xls');

        if( $format == 'xls' ) {
            $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        }
        else {
            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        }

        $objWriter->save($sFileName);

        return true;
    }

    /**
     * Номер столбца в символьное имя
     * @param int $index
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function colIndexToName($index = 0) {
        if( $index <= 0 ) {
            throw new InvalidArgumentException('Некорректный номер столбца');
        }
        $sName = '';
        while( $index > 0 ) {
            $sName = chr(ord('A') - 1 + $index % 26) . $sName;
            $index = intval($index / 26);
        }
        return $sName;
    }

    /**
     * @param string $sDir
     * @param string $sExt
     * @param integer $nTime
     */
    public function clearDestinationDir($sDir, $sExt, $nTime) {
        if( $hd = opendir($sDir) ) {
            while( false !== ($f = readdir($hd)) ) {
                if( ($f == '.') || ($f == '..') ) {
                    continue;
                }

                $ext = $this->getFileExt($f);
                if( $ext != $sExt ) {
                    continue;
                }

                $sf = $sDir . DIRECTORY_SEPARATOR . $f;
                if( filemtime($sf) < $nTime ) {
                    unlink($sf);
                }
            }
            closedir($hd);
        }
    }

    /**
     * @param string $sFile
     * @param string $sDefault
     * @return string
     */
    public function getFileExt($sFileName, $sDefault = '') {
        $p = strrpos($sFileName, '.');
        return ($p === false) ? $sDefault : substr($sFileName, $p+1);
    }
}