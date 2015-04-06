<?php

use yii\db\Schema;
use yii\db\Migration;

class m150406_120016_add_msg_curator_user extends Migration
{
    public function up() {
        $this->addColumn('{{%message}}', 'msg_curator_id', Schema::TYPE_INTEGER);
        $this->refreshCache();
    }

    public function down() {
        $this->dropColumn('{{%message}}', 'msg_curator_id');
        $this->refreshCache();
        return true;
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
