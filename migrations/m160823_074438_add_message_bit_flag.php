<?php

use yii\db\Schema;
use yii\db\Migration;

class m160823_074438_add_message_bit_flag extends Migration
{
    public function up()
    {
        $this->addColumn('{{%message}}', 'msg_bitflag', Schema::TYPE_INTEGER . ' COMMENT \'Битовые флаги\'');
        $this->refreshCache();
    }

    public function down()
    {
        $this->dropColumn('{{%message}}', 'msg_bitflag');
        $this->refreshCache();
    }

    public function refreshCache()
    {
        Yii::$app->db->schema->refresh();
        Yii::$app->db->schema->getTableSchemas();
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
