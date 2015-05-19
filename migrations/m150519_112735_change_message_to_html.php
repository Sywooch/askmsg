<?php

use yii\db\Schema;
use yii\db\Migration;
use yii\helpers\Html;

class m150519_112735_change_message_to_html extends Migration
{
    public function up()
    {
        $this->convert(true);
    }

    public function down()
    {
        $this->convert(false);
    }


    public function convert($encode = true)
    {
        $db = Yii::$app->db;

        $sSql = 'SELECT msg_id, msg_pers_text FROM educom_message';
        $sSqlUpdate = 'Update educom_message Set msg_pers_text = :stext Where msg_id = :msgid';
        $oReader = $db->createCommand($sSql)->query();

        $oCommand = $db->createCommand($sSqlUpdate);

        $oCommand->bindParam(':msgid', $msgid);
        $oCommand->bindParam(':stext', $stext);

        $options = [];
        $nCou = 0;
        $nCouDiff = 5;

        while( $row = $oReader->read() ) {
            $stext = $encode ? Html::encode($row['msg_pers_text']) : Html::decode($row['msg_pers_text']);
            $msgid = $row['msg_id'];
            $oCommand->execute();
            $nCou++;
            if( $nCou %1000 == 0 ) {
                echo "Updated: {$nCou}\n";
            }
            if( $nCouDiff < 0 ) {
                continue;
            }
            if( strlen($stext) != strlen($row['msg_pers_text']) ) {
                $n1 = strlen($stext);
                $n2 = strlen($row['msg_pers_text']);
                $i1 = $i2 = 0;
                while( ($i1 < $n1) && ($i2 < $n2) ) {
                    if( mb_substr($stext, $i1, 1, 'UTF-8') != mb_substr($row['msg_pers_text'], $i1, 1, 'UTF-8') ) {
                        echo iconv('UTF-8','CP866', "$nCouDiff: {$msgid} [{$i1}] : " . mb_substr($row['msg_pers_text'], $i1, 10, 'UTF-8') . ' -> ' . mb_substr($stext, $i1, 10, 'UTF-8') . "\n");
                        break;
                    }
                    $i1++;
                    $i2++;
                }
                $nCouDiff--;
            }
        }
        echo "Updated: {$nCou}\n";
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
