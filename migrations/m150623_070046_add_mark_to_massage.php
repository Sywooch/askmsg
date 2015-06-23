<?php

use yii\db\Schema;
use yii\db\Migration;

class m150623_070046_add_mark_to_massage extends Migration
{
    public function up()
    {

        $this->addColumn('{{%message}}', 'msg_mark', Schema::TYPE_INTEGER);
        $this->refreshCache();
    }

    public function down()
    {
        $this->dropColumn('{{%message}}', 'msg_mark');
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
