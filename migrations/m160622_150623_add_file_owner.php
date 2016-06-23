<?php

use yii\db\Schema;
use yii\db\Migration;

class m160622_150623_add_file_owner extends Migration
{
    public function up()
    {
        $this->addColumn(
            '{{%file}}',
            'file_answer_id',
            Schema::TYPE_INTEGER . ' Default 0'
        );

        $sSql = 'Alter Table {{%file}} DISABLE KEYS';
        Yii::$app->db->createCommand($sSql)->execute();


//        $sSql = 'Update {{%file}} Set file_answer_id = If(file_user_id = 0, 0, ) Where file_id > 0';
//        $this->db->createCommand($sSql)->execute();

        $command = Yii::$app->db->createCommand('SELECT COUNT(*) FROM {{%file}} Where file_user_id > 0');
        $nAnswerCou = $command->queryScalar();

        $nSelectSize = 20;
        $nMaxId = 0;
        $bNext = true;
        $nCou = 0;
        while( $bNext ) {
            $bNext = false;
            $command = Yii::$app->db->createCommand('SELECT * FROM {{%file}} Where file_user_id > 0 And file_id > ' . $nMaxId . ' Order By file_id Limit ' . $nSelectSize);
            $aData = $command->queryAll(PDO::FETCH_ASSOC);
            foreach( $aData As $row ) {
                $nMaxId = $row['file_id'];
                $bNext = true;

                $command = Yii::$app->db->createCommand('SELECT * FROM {{%answer}} Where ans_ap_id = ' . $row['file_msg_id']);
                $aAnswer = $command->queryOne(PDO::FETCH_ASSOC);

                $sSql = 'Update {{%file}} Set file_answer_id = '.$aAnswer['ans_id'].' Where file_id = ' . $nMaxId;
                $this->db->createCommand($sSql)->execute();

                $nCou++;
                if( $nCou % 10 == 0 ) {
                    $this->printStr('Update files: ' . $nCou . '/' . $nAnswerCou . "\n");
                }
            }

        }

        $this->printStr('Update files: ' . $nCou . "\n");

        $sSql = 'Alter Table {{%file}} ENABLE KEYS';
        Yii::$app->db->createCommand($sSql)->execute();

        $this->createIndex('idx_file_answer_id', '{{%file}}', 'file_answer_id');
        $this->refreshCache();

    }

    public function down()
    {
        $this->dropColumn(
            '{{%file}}',
            'file_answer_id'
        );
        $this->refreshCache();
    }

    public function refreshCache()
    {
        Yii::$app->db->schema->refresh();
        Yii::$app->db->schema->getTableSchemas();
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
