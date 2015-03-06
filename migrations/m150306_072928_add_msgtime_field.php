<?php

use yii\db\Schema;
use yii\db\Migration;

class m150306_072928_add_msgtime_field extends Migration
{
    public function up()
    {
        $this->addColumn('{{%message}}', 'msg_limittime', Schema::TYPE_DATETIME);

        $tableOptionsMyISAM = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';

        // create message date logs table
        $this->createTable('{{%msgtime}}', [
            'mt_id' => Schema::TYPE_PK,
            'mt_msg_flag' => Schema::TYPE_INTEGER,
            'mt_start_time' => Schema::TYPE_DATETIME,
            'mt_finish_time' => Schema::TYPE_DATETIME,
            'mt_start_uid' => Schema::TYPE_INTEGER,
            'mt_finish_uid' => Schema::TYPE_INTEGER,
        ], $tableOptionsMyISAM);
    }

    public function down()
    {
        $this->dropTable('{{%msgtime}}');
        $this->dropColumn('{{%message}}', 'msg_limittime');

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
