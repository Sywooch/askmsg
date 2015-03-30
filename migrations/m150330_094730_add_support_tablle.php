<?php

use yii\db\Schema;
use yii\db\Migration;

class m150330_094730_add_support_tablle extends Migration
{
    public function up()
    {
        $tableOptionsMyISAM = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';

        // create user table
        $this->createTable('{{%support}}', [
            'sup_id' => Schema::TYPE_PK,
            'sup_createtime' => Schema::TYPE_DATETIME,
            'sup_message' => Schema::TYPE_TEXT . ' NOT NULL',
            'sup_empl_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'sup_active' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_support_create', '{{%support}}', 'sup_createtime');
        $this->createIndex('idx_support_active', '{{%support}}', 'sup_active');
        $this->createIndex('idx_support_user', '{{%support}}', 'sup_empl_id');
    }

    public function down()
    {
        $this->dropTable('{{%support}}');
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
