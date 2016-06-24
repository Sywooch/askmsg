<?php

use yii\db\Schema;
use yii\db\Migration;

class m160624_063602_add_mediate_answer_finish extends Migration
{
    public function up()
    {
        $this->addColumn('{{%mediateanswer}}', 'ma_finished', Schema::TYPE_DATETIME . ' COMMENT \'Завершен\'');
        $this->refreshCache();
    }

    public function down()
    {
        $this->dropColumn('{{%mediateanswer}}', 'ma_finished');
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
