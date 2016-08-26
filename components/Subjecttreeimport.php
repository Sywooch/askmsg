<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 26.08.2016
 * Time: 14:20
 */

namespace app\components;

use yii;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Cell;
use app\models\SubjectTree;

class Subjecttreeimport {

    /**
     *
     * Импортируем данные из xls файла
     *
     * @param string $sDir папка с файлами
     */
    public function importDir($sDir = '') {
        $aFiles = glob($sDir . DIRECTORY_SEPARATOR . '*.xlsx');

        echo nl2br(print_r($aFiles, true));

        $sTable = SubjectTree::tableName();
        Yii::$app->db->createCommand('Delete From ' . $sTable . ' Where subj_id > 0')->execute();
        Yii::$app->db->createCommand('Alter Table ' . $sTable . ' AUTO_INCREMENT = 1')->execute();


        foreach($aFiles As $k=>$sf) {
            $aData = $this->getFileData($sf);
            echo nl2br(print_r($aData, true));
//            $aData
            if($k > 2) {
                break;
            }
        }

        return $aFiles;
    }

    /**
     * @param string $sf
     */
    public function getFileData($sf = '') {
        if( empty($sf) || !file_exists($sf) ) {
            return null;
        }

        $objPHPExcel = PHPExcel_IOFactory::load($sf);
        $oSheet = $objPHPExcel->getSheet(0);
        $nMaxRow = $oSheet->getHighestDataRow();
        $sLastCol = $oSheet->getHighestDataColumn();

//        echo iconv('CP866', 'UTF-8', $sf) . "<br />";
//        echo mb_convert_encoding($sf, "windows-1251", "utf-8");

        if( $sLastCol != 'F' ) {
//            echo iconv('CP866', 'UTF-8', $sf) . "lastcol = {$sLastCol}<br />";
            echo $sf . " lastcol = {$sLastCol}<br />";
            return;
        }
        $sFirstCol = 'A';
        $nStartRow = 1;

        for( $row = $nStartRow; $row <= $nMaxRow; $row++ ) {
//            $aRow = $oSheet->rangeToArray($sFirstCol . $row . ':' . $sLastCol . $row);
            if( $row == $nStartRow ) {
                $aTitles = $oSheet->rangeToArray($sFirstCol . $row . ':' . $sLastCol . $row);
                continue;
            }

            $oCell = $oSheet->getCellByColumnAndRow(0, $row);
            $sProblemRange = $this->getMergeRange($oCell);
            $nNewRow = 0;
            if( $sProblemRange ) {
                $sValue = $this->getMergeValue($oSheet, $sProblemRange);
                $aMerged = PHPExcel_Cell::extractAllCellReferencesInRange($sProblemRange);
                $sFirstCell = $aMerged[0];
                $sLastCell = array_pop($aMerged);
                $nFirstRow = intval(preg_replace('|[^\\d]|', '', $sFirstCell));
                $nLastRow = intval(preg_replace('|[^\\d]|', '', $sLastCell));
            }
            else {
                $sValue = $oCell->getValue();
                $sProblemRange = '';
                $nFirstRow = $row;
                $nLastRow = $row;
            }
//            echo $sValue . ' problem: ' . print_r($sProblemRange, true) . '<br />';

            for($sferaRow = $nFirstRow; $sferaRow <= $nLastRow; $sferaRow++ ) {
                $oSfera = $oSheet->getCellByColumnAndRow(1, $sferaRow);
//                echo $oSfera->getValue() . ' ('.$sferaRow.') ' . '<br />';
                $sSferaRange = $this->getMergeRange($oSfera);
                echo 'sSferaRange = '.$sSferaRange.' ('.$sferaRow.') ' . '<br />';
                if( $sSferaRange ) {
                    $sSferaValue = $this->getMergeValue($oSheet, $sSferaRange);
                    $aMerged = PHPExcel_Cell::extractAllCellReferencesInRange($sSferaRange);
                    $sFirstCell = $aMerged[0];
                    $sLastCell = array_pop($aMerged);
                    $nSferaFirstRow = intval(preg_replace('|[^\\d]|', '', $sFirstCell));
                    $nSferaLastRow = intval(preg_replace('|[^\\d]|', '', $sLastCell));
                }
                else {
                    $sSferaValue = $oSfera->getValue();
                    $sSferaRange = '';
                    $nSferaFirstRow = $sferaRow;
                    $nSferaLastRow = $sferaRow;
                }

//                echo '...... ' . $sSferaValue . ' cfera ' . $sferaRow . ' ' . print_r($sSferaRange, true) . '<br />';
                for($voprosRow = $nSferaFirstRow; $voprosRow <= $nSferaLastRow; $voprosRow++ ) {
                    $oVopros = $oSheet->getCellByColumnAndRow(2, $voprosRow);
//                    echo '............ ' . $oVopros->getValue() . ' ('.$voprosRow.')<br />';
                    $oInfo = $oSheet->getCellByColumnAndRow(3, $voprosRow);
                    $sInfo = $this->getMergeRange($oInfo);
                    $oLastQuest = $oSheet->getCellByColumnAndRow(4, $voprosRow);
                    $sLastQuest = $this->getMergeRange($oLastQuest);
                    $this->addRow([
                        'problem' => $sValue,
                        'sfera' => $sSferaValue,
                        'vopros' => $oVopros->getValue(),
                        'info' => $sInfo ? $this->getMergeValue($oSheet, $sInfo) : $oInfo->getValue(),
                        'lastvopros' => $sLastQuest ? $this->getMergeValue($oSheet, $sLastQuest) : $oLastQuest->getValue(),
                    ]);

                }
                $row = $nSferaLastRow;
                $sferaRow = $nSferaLastRow;
//                break;
            }
//            break;
        }

//        $aData = $oSheet->rangeToArray($sFirstCol . $nStartRow . ':' . $sLastCol . $nMaxRow);
//        return $aData;
    }

    /**
     *
     * Это для определения - объединенная ли ячейка,
     * взято из последнего PHPExcel
     *
     * @param $oCell
     * @return bool
     */
    public function getMergeRange($oCell) {
        foreach($oCell->getWorksheet()->getMergeCells() as $mergeRange) {
            if ($oCell->isInRange($mergeRange)) {
                return $mergeRange;
            }
        }
        return false;
    }

    /**
     *
     * Получаем значение из объединенного диапазона ячеек (первое непустое)
     *
     * @param $oSheet
     * @param $sRange
     * @return string | null
     */
    public function getMergeValue($oSheet, $sRange) {
        $a = $oSheet->rangeToArray($sRange);
        $sRet = null;
        foreach($a As $aRow) {
            foreach($aRow As $val) {
                if( !empty($val) ) {
                    $sRet = $val;
                    break;
                }
            }
            if( $sRet !== null ) {
                break;
            }
        }
        return $sRet;
    }

    /**
     * @param array $aData
     */
    public function addRow($aData) {
        echo nl2br(print_r($aData, true)) . '<br />';
        $ob = SubjectTree::find()
            ->where([
                'subj_variant' => $aData['problem'],
            ])
            ->one();
        if( $ob === null ) {
            $oParent = null;
            $ob = $this->insertNode($oParent, ['subj_variant' => $aData['problem']]);
        }

        if( empty($aData['vopros']) ) {
            $this->insertNode(
                $ob,
                [
                    'subj_variant' => $aData['sfera'],
                    'subj_info' => $aData['info'],
                    'subj_final_question' => $aData['lastvopros'],
                ]
            );
        }
        else {
            $obSfera = SubjectTree::find()
                ->where([
                    'subj_variant' => $aData['sfera'],
                ])
                ->one();
            if( $obSfera === null ) {
                $obSfera = $this->insertNode(
                    $ob,
                    [
                        'subj_variant' => $aData['sfera'],
                    ]
                );
            }
            $this->insertNode(
                $obSfera,
                [
                    'subj_variant' => $aData['vopros'],
                    'subj_info' => $aData['info'],
                    'subj_final_question' => $aData['lastvopros'],
//                    'subj_final_person' => $aData['problem'],
                ]
            );
        }
    }

    /**
     * @param SubjectTree $oParent
     * @param array $aData
     */
    public function insertNode($oParent, $aData) {
        $sTable = SubjectTree::tableName();
        if( $oParent === null ) {
            $nMax = Yii::$app->db->createCommand('Select MAX(subj_rgt) From ' . $sTable )->queryScalar();
            if( $nMax === null ) {
                $nMax = 0;
            }
            $nMax++;
        }
        else {
            $nMax = $oParent->subj_rgt;
        }

        $oNew = new SubjectTree();

        $oNew->subj_parent_id = $oParent === null ? 0 : $oParent->subj_id;
        $oNew->subj_level = $oParent === null ? 0 : ($oParent->subj_level + 1);
        $sSql = 'UPDATE ' . $sTable . ' SET subj_rgt = subj_rgt + 2, subj_lft = IF(subj_lft > '.$nMax.', subj_lft + 2, subj_lft) WHERE subj_rgt >= '.$nMax.'';
        Yii::$app->db->createCommand($sSql)->execute();

        $oNew->subj_lft = $nMax;
        $oNew->subj_rgt = $nMax + 1;
        $oNew->subj_variant = $aData['subj_variant'];
        foreach(['subj_info', 'subj_final_question', 'subj_final_person',] As $fld) {
            if( isset($aData[$fld]) ) {
                $oNew->{$fld} = $aData[$fld];
            }
        }
        $oNew->save();

        return $oNew;
    }

}