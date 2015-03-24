<?php

use yii\db\Schema;
use yii\db\Migration;

class m150312_122050_add_file_table extends Migration
{
    public function up()
    {
/*
        $tableOptionsMyISAM = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';

        $this->createTable('{{%file}}', [
            'file_id' => Schema::TYPE_PK,
            'file_time' => Schema::TYPE_DATETIME,
            'file_orig_name' => Schema::TYPE_STRING . ' NOT NULL',
            'file_msg_id' => Schema::TYPE_INTEGER,
            'file_user_id' => Schema::TYPE_INTEGER,
            'file_size' => Schema::TYPE_INTEGER . ' NOT NULL',
            'file_type' => Schema::TYPE_STRING,
            'file_name' => Schema::TYPE_STRING . ' NOT NULL',
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_file_msg_id', '{{%file}}', 'file_msg_id');
*/
    }

    public function down()
    {
//        $this->dropTable('{{%file}}');

        return true;
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
