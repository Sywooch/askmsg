<?php

use yii\db\Schema;
use yii\db\Migration;
use yii\db\Query;
use app\models\Stateflag;

class m160620_141248_import_messages extends Migration
{
    public $aTables = [
        '{{%answer}}' => 'ans_id',
        '{{%appeal}}' => 'ap_id',
    ];

    public function up()
    {
        $t1 = time();
        $this->down();

        foreach($this->aTables As $k=>$v) {
            $sSql = 'Alter Table '.$k.' DISABLE KEYS';
            Yii::$app->db->createCommand($sSql)->execute();
        }

        $nCou = 0;
        $sSqlAsk = '';
        $sSqlAns = '';
        $aAskParams = [];
        $aAnsParams = [];
        $nGroup = 10;
        $nSelectSize = 100;

        $nMaxId = 0;
        $bNext = true;
        $base_memory_usage = memory_get_usage();

        while( $bNext ) {
            $bNext = false;
            $command = Yii::$app->db->createCommand('SELECT * FROM {{%message}} Where msg_id > ' . $nMaxId . ' Limit ' . $nSelectSize);
            $aData = $command->queryAll(PDO::FETCH_ASSOC);
//            echo "Records: {$reader->rowCount}\n";
//            while( $row = $reader->read() ) {
            foreach( $aData As $row ) {
                $nMaxId = $row['msg_id'];
                $bNext = true;
                if( $nCou % $nGroup == 0 ) {
                    $this->printStr('Memory usage 1: ' . (memory_get_usage() - $base_memory_usage) . "\n");

                    if( $sSqlAsk != '' ) {
                        $nAffected = $this->runSql($sSqlAsk, $aAskParams);
                        $this->printStr('Affected ask: ' . $nAffected . " [{$nCou}]\n");
                        $sSqlAsk = '';
                        $aAskParams = [];
                    }
                    if( $sSqlAns != '' ) {
                        $nAffected = $this->runSql($sSqlAns, $aAnsParams);
//                        $oCommand = Yii::$app->db->createCommand($sSqlAns, $aAnsParams);
//                        $nAffected = $oCommand->execute();
//                        $oCommand->cancel();
//                        unset($oCommand);
                        $this->printStr('Affected ans: ' . $nAffected . "\n");
                        $sSqlAns = '';
                        $aAnsParams = [];
                    }

                    $this->printStr('Memory usage 1: ' . (memory_get_usage() - $base_memory_usage) . "\n");
//                if( $nCou > 0 ) {
//                    break;
//                }
                }
//            $this->printStr(print_r($row, true));
                $aMessageData = $this->prepareData($row);

                // вопросы вставляем
                $sFields = '';
                $sVals = '';
                foreach($aMessageData['ask'] As $k => $v) {
                    $sFields .= $k . ', ';
                    $sParam = ':' . str_replace('ap_', '', $k) . '_' . $nCou;
                    $sVals .= $sParam . ', ';
                    $aAskParams[$sParam] = $v;
                }
                $sVals = '(' . substr($sVals, 0, -2) . ')';
                if( $sSqlAsk == '' ) {
                    $sSqlAsk = 'Insert Into {{%appeal}} (' . substr($sFields, 0, -2) . ') Values ' . $sVals;
                }
                else {
                    $sSqlAsk .= ', ' . $sVals;
                }
//            $this->printStr(print_r($aMessageData, true));
//            $this->printStr(print_r($sSqlAsk, true) . "\n");

                // ответы вставляем
                if( $aMessageData['ans'] !== null ) {
                    $sFields = '';
                    $sVals = '';
                    foreach($aMessageData['ans'] As $k => $v) {
                        $sFields .= $k . ', ';
                        $sParam = ':' . str_replace('ans_', '', $k) . '_' . $nCou;
                        $sVals .= $sParam . ', ';
                        $aAnsParams[$sParam] = $v;
                    }
                    $sVals = '(' . substr($sVals, 0, -2) . ')';
                    if( $sSqlAns == '' ) {
                        $sSqlAns = 'Insert Into {{%answer}} (' . substr($sFields, 0, -2) . ') Values ' . $sVals;
                    }
                    else {
                        $sSqlAns .= ', ' . $sVals;
                    }
//                $this->printStr(print_r($sSqlAns, true) . "\n");
                }

                unset($aMessageData);

                $nCou++;
//            $rows[] = $row;
            }
        }

        if( $sSqlAsk != '' ) {
            $nAffected = $this->runSql($sSqlAsk, $aAskParams);
            $this->printStr('Affected ask: ' . $nAffected . " [{$nCou}]\n");
        }
        if( $sSqlAns != '' ) {
            $nAffected = $this->runSql($sSqlAns, $aAnsParams);
            $this->printStr('Affected ans: ' . $nAffected . "\n");
        }

        foreach($this->aTables As $k=>$v) {
            $sSql = 'Alter Table '.$k.' ENABLE KEYS';
            Yii::$app->db->createCommand($sSql)->execute();
        }

        $t2 = time() - $t1;
        $this->printStr("Time: " . sprintf("%02d:%02d", $t2 / 60, $t2 % 60) . "\n");

        return false;
    }

    public function down()
    {
        foreach($this->aTables As $k=>$v) {
            $sSql = 'Delete From '.$k.' Where '.$v.' > 0';
            Yii::$app->db->createCommand($sSql)->execute();

            $sSql = 'Alter Table '.$k.' AUTO_INCREMENT = 1';
            Yii::$app->db->createCommand($sSql)->execute();
        }

    }

    /**
     * @param $sSql
     * @param $aParams
     * @return int
     * @throws \yii\db\Exception
     */
    public function runSql($sSql, $aParams) {
        $oCommand = Yii::$app->db->createCommand($sSql, $aParams);
        $nAffected = $oCommand->execute();
        $oCommand->cancel();
        unset($oCommand);
        return $nAffected;
    }
    /**
     * @param $row
     * @return array
     */
    public function prepareData($row) {
        $aMsg = [
            'ap_id' => $row['msg_id'],
            'ap_created' => $row['msg_createtime'],
//            'ap_next_act_date' => $row[''],
//            'ap_pers_name' => $row[''],
//            'ap_pers_secname' => $row[''],
//            'ap_pers_lastname' => $row[''],
//            'ap_pers_email' => $row[''],
//            'ap_pers_phone' => $row[''],
//            'ap_pers_org' => $row[''],
//            'ap_pers_region' => $row[''],
//
//            'ap_pers_text' => $row[''],
//            'ap_empl_command' => $row[''],
//            'ap_comment' => $row[''],
//
//            'ap_subject' => $row[''],
//            'ap_empl_id' => $row[''],
//            'ap_curator_id' => $row[''],
//            'ekis_id' => $row[''],
//
//            'ap_state' => $row[''],
//            'ap_is_archive' => $row[''],
        ];

        // тут выставляем даты обращения: завершения, следующего действия
        if( in_array($row['msg_flag'], [4, 6, 7, 11]) ) {
//            $aMsg['ap_finished'] = empty($row['msg_answertime']) ? $row['msg_createtime'] : $row['msg_answertime'];
            $aMsg['ap_next_act_date'] = null;
        }
        else {
            // TODO: тут посмотреть флаги и поставить даты в зависимости от новизны или наличия поручения
            // пока просто быстрый вариант стоит
//            $aMsg['ap_finished'] = null;
            $aMsg['ap_next_act_date'] = date('Y-m-d H:i:s', strtotime($row['msg_createtime']) + 24 * 3600);
        }

        // флаги обращения
        $sAnswerFlag = Stateflag::STATE_ANSWER_NOT_NEED;
        if( in_array($row['msg_flag'], [1]) ) { // новые
            $sAppealFlag = Stateflag::STATE_APPEAL_NEW;
            $sAnswerFlag = Stateflag::STATE_ANSWER_NONE;
        }
        else if( in_array($row['msg_flag'], [7]) || ($row['msg_active'] == 0) ) { // удаленные
            $sAppealFlag = Stateflag::STATE_APPEAL_DELETED;
        }
        else if( in_array($row['msg_flag'], [2, 3, 4, 5, 6, 11, 13, ]) ) { // поручения
            $sAppealFlag = Stateflag::STATE_APPEAL_PUBLIC;
            switch($row['msg_flag']) {
                case 2: // поручение
                    $sAnswerFlag = Stateflag::STATE_ANSWER_NONE;
                    break;
                case 3: // ответ готов
                    $sAnswerFlag = Stateflag::STATE_ANSWER_NEW;
                    break;
                case 4: // ответ опубликован
                    $sAnswerFlag = Stateflag::STATE_ANSWER_MODERATED;
                    break;
                case 5: // на доработку
                    $sAnswerFlag = Stateflag::STATE_ANSWER_TOFIX;
                    break;
                case 6: // Опубликованные без ответов
                    $sAnswerFlag = Stateflag::STATE_ANSWER_NOT_NEED;
                    break;
                case 11: // Благодарности
                    $sAnswerFlag = empty($row['msg_answer']) ? Stateflag::STATE_ANSWER_NOT_NEED : Stateflag::STATE_ANSWER_MODERATED;
                    break;
                case 13: // Поручение, ответ не согласован
                    $sAnswerFlag = Stateflag::STATE_ANSWER_NEW;
                    break;
            }
        }
        else { // внутренние поручения
            $sAppealFlag = Stateflag::STATE_APPEAL_PRIVATE;
            switch($row['msg_flag']) {
                case 8: // Внутренние поручения
                    $sAnswerFlag = Stateflag::STATE_ANSWER_NONE;
                    break;
                case 9: // Внутренние ответы
                    $sAnswerFlag = Stateflag::STATE_ANSWER_NEW;
                    break;
                case 10: // На доработку в.п.
                    $sAnswerFlag = Stateflag::STATE_ANSWER_TOFIX;
                    break;
                case 12: // Выполненные внутренние поручения
                    $sAnswerFlag = Stateflag::STATE_ANSWER_MODERATED;
                    break;
                case 14: // ВП, ответ не согласован
                    $sAnswerFlag = Stateflag::STATE_ANSWER_NEW;
                    break;
            }
        }

        // тут остатки полей присваиваем
        $a = [
            'pers_name',
            'pers_secname',
            'pers_lastname',
            'pers_email',
            'pers_phone',
            'pers_org',
            'pers_region',
            'pers_text',
            'empl_command',
            'comment',
            'subject',
            'empl_id',
            'curator_id',
        ];
        foreach($a As $v) {
            $aMsg['ap_' . $v] = $row['msg_' . $v];
        }

        $aMsg['ekis_id'] = $row['ekis_id'];
        $aMsg['ap_state'] = $sAppealFlag;
        $aMsg['ap_ans_state'] = $sAnswerFlag;

        //******************************************************************** данные для ответа
        $aAns = null;
        $data = false;
        if( !empty($row['msg_answer']) ) {
            if( ($row['msg_mark'] !== null) && ($row['msg_mark'] == 0) ) {
                $sFind = 'На мое обращение № '.$row['msg_id'].' от '.date('d.m.Y', strtotime($row['msg_createtime'])).' был получен следующий ответ';

                $query = new Query;// compose the query
                $query->select('*')
                    ->from('{{%message}}')
                    ->where(['like', 'msg_pers_text', $sFind]);
                $data = $query->createCommand()->queryOne(PDO::FETCH_ASSOC);
            }
            $aAns = [
//            'ans_id' => $row[''],
                'ans_created' => empty($row['msg_answertime']) ? $row['msg_createtime'] : $row['msg_answertime'],

                'ans_text' => $row['msg_answer'],
                'ans_remark' => $row['msg_empl_remark'],

                'ans_type' => Stateflag::TYPE_ANSWER_FINAL, // промежуточный или окончательный
                'ans_state' => $sAnswerFlag,
                'ans_ap_id' => $row['msg_id'],
                'ans_us_id' => $row['msg_empl_id'],
                'ans_mark' => $row['msg_mark'],
                'ans_mark_comment' => null,
            ];

            if( $data !== false ) {
                $aAns['ans_mark_comment'] = $data['msg_pers_text'];
            }

        }
        return [
            'ask' => $aMsg,
            'ans' => $aAns,
        ];
    }

    public function printStr($s)
    {
        if( DIRECTORY_SEPARATOR == '\\' ) {
            $s = mb_convert_encoding($s, 'CP-866');
        }
        echo $s;
    }


    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }
    
    public function safeDown()
    {
    }
    */
}
