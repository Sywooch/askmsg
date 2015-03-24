<?php

use yii\db\Schema;
use yii\db\Migration;

class m150319_072507_add_file_index extends Migration
{
    public function up()
    {
//        $this->createIndex('idx_file_name', '{{%file}}', 'file_name');
        $this->refreshCache();
    }

    public function down()
    {
//        $this->dropIndex('idx_file_name', '{{%file}}');
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
