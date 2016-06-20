<?php

use yii\db\Schema;
use yii\db\Migration;
use app\models\Stateflag;

class m160620_141248_import_messages extends Migration
{
    public $aTables = [
        '{{%answer}}' => 'ans_id',
        '{{%appeal}}' => 'ap_id',
    ];

    public function up()
    {
        foreach($this->aTables As $k=>$v) {
            $sSql = 'Alter Table '.$k.' DISABLE KEYS';
            Yii::$app->db->createCommand($sSql)->execute();
        }

        $command = Yii::$app->db->createCommand('SELECT * FROM {{%message}}');
        $reader = $command->query();
        echo "Records: {$reader->rowCount}\n";

        while ($row = $reader->read()) {
            $this->printStr(print_r($row, true));
            $aMessageData = $this->prepareData($row);
            $this->printStr(print_r($aMessageData, true));
            break;
//            $rows[] = $row;
        }

        foreach($this->aTables As $k=>$v) {
            $sSql = 'Alter Table '.$k.' ENABLE KEYS';
            Yii::$app->db->createCommand($sSql)->execute();
        }

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

    public function prepareData($row) {
        $aMsg = [
            'ap_id' => $row['msg_id'],
            'ap_created' => $row['msg_createtime'],
//            'ap_next_act_date' => $row[''],

            'ap_pers_name' => $row[''],
            'ap_pers_secname' => $row[''],
            'ap_pers_lastname' => $row[''],
            'ap_pers_email' => $row[''],
            'ap_pers_phone' => $row[''],
            'ap_pers_org' => $row[''],
            'ap_pers_region' => $row[''],

            'ap_pers_text' => $row[''],
            'ap_empl_command' => $row[''],
            'ap_comment' => $row[''],

            'ap_subject' => $row[''],
            'ap_empl_id' => $row[''],
            'ap_curator_id' => $row[''],
            'ekis_id' => $row[''],

            'ap_state' => $row[''],
            'ap_is_archive' => $row[''],
        ];

        // тут выставляем даты обращения: завершения, следующего действия
        if( in_array($row['msg_flag'], [4, 6, 7, 11]) ) {
            $aMsg['ap_finished'] = empty($row['msg_answertime']) ? $row['msg_createtime'] : $row['msg_answertime'];
        }
        else {
            // TODO: тут посмотреть флаги и поставить даты в зависимости от новоизны или наличия поручения
            // пока просто быстрый вариант стоит
            $aMsg['ap_next_act_date'] = date('Y-m-d H:i:s', strtotime($row['msg_createtime']) + 24 * 3600);
        }

        // флаги обращения
        if( in_array($row['msg_flag'], [1]) ) {
            $aMsg['ap_state'] = Stateflag::STATE_APPEAL_NEW;
        }
        else if( in_array($row['msg_flag'], [7]) || ($row['msg_active'] == 0) ) {
            $aMsg['ap_state'] = Stateflag::STATE_APPEAL_DELETED;
        }
        else if( in_array($row['msg_flag'], [2, 3, 4, 5, 6, 11, 13, ]) ) {
            $aMsg['ap_state'] = Stateflag::STATE_APPEAL_PUBLIC;
        }
        else {
            $aMsg['ap_state'] = Stateflag::STATE_APPEAL_PRIVATE;
        }

        // флаг архива
        $aMsg['ap_is_archive'] = (in_array($row['msg_flag'], [7]) || ($row['msg_active'] == 0)) ? 1 : 0;

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
